<?php

namespace Taf;

use PDO;

class TableDocumentation extends TafConfig
{
    public $url = "";
    public $description = [];
    public $table_descriptions = array("table_name" => "", "les_colonnes" => []);
    public $table_name = "";
    public function __construct($table_name)
    {
        $this->table_name = $table_name;
        $this->table_descriptions["table_name"] = $table_name;
        $this->check_mode_deploiement();
        $this->url = $this->get_base_url();
        $this->init_data();
    }
    function init_data()
    {
        switch ($this->database_type) {
            case 'pgsql':
                $this->description = $this->get_db()->query("select column_name from information_schema.columns where table_name = '{$this->table_name}'")->fetchAll(PDO::FETCH_COLUMN);
                break;
            case 'mysql':
                $this->description = $this->get_db()->query("desc {$this->table_name}")->fetchAll(PDO::FETCH_COLUMN);
                $this->table_descriptions = $this->get_table_descriptions($this->table_name, [$this->table_name]);
                break;
            case 'sqlsrv':
                $this->description = $this->get_db()->query("select column_name from information_schema.columns where table_name = '{$this->table_name}' order by ordinal_position")->fetchAll(PDO::FETCH_COLUMN);
                break;
            default:
                // type de base de données inconnu
                break;
        }
    }
    function check_mode_deploiement()
    {
        if (self::$mode_deploiement) {
            echo "<h1>Mode déploiement activé</h1>";
            exit;
        }
    }
    public function get_base_url()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https://";
        } else {
            $url = "http://";
        }
        // Append the host(domain name, ip) to the URL.   
        $url .= $_SERVER['HTTP_HOST'];

        // Append the requested resource location to the URL   
        $url .= $_SERVER['REQUEST_URI'];

        return $url;
    }

    public function get()
    {
        return <<<HTML
                <div class="d-flex justify-content-between">
                     <h3 class="mb-2 mt-3">Get</h4>
                    <!-- Example single danger button -->
                    <button type="button" class="btn btn-soft-light">
                        <a href="get">
                        Tester l'api
                        </a>
                    </button>
                </div>
                   <p class="fs-5">
                   L'action "get" vous ouvre les portes pour accéder aux données de la table <span class="text-danger">{$this->table_name}</span>. Grâce à la fonction ci-dessous, vous pouvez récupérer une donnée spécifique ou l'intégralité des données avec une aisance remarquable. <br>
                  Cette fonction requiert une manipulation précise, vous permettant de cibler une donnée particulière ou d'extraire l'ensemble des données de manière fluide et efficace. Un outil puissant pour répondre à vos besoins d'information.
                    </p>           
                <div class="ace_js">
                get_$this->table_name(){
                    let params={}// les conditions à mettre ici
                    this.loading_edit_{$this->table_name} = true;
                    this.api.taf_post("{$this->table_name}/get",params,(reponse:any)=>{
                        //when success
                        if(reponse.status){
                            console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ",reponse);
                        }else{
                            console.log("L\'opération sur la table {$this->table_name} a échoué. Réponse= ",reponse);
                        }
                        this.loading_edit_{$this->table_name} = false;
                    },
                    (error:any)=>{
                        //when error
                        this.loading_edit_{$this->table_name} = false;
                        console.log("Erreur inconnue! ",error);
                    })
                }
               </div>
        HTML;
    }
    public function getParamsForAdd()
    {
        $keysValues = array();
        foreach ($this->description as $key => $value) {
            $keysValues[] = $value;
        }
        $ts_object = implode(",\n\t\t", $keysValues);
        return <<<HTML
                    <div id="json_add" class="col-12">
                    {
                        $ts_object
                    }
                    </div>
                HTML;
    }


    public function add_form()
    {
        $keysValues = array();
        foreach ($this->table_descriptions["les_colonnes"] as $key => $une_colonne) {
            $type = 'text';
            switch ($une_colonne["Key"]) {
                case 'PRI': // rien pou la cle primaire
                    break;
                case 'MUL': // un champs de type select pour les cles etraangeres
                    $keysValues[] = '
                    &lt;!-- champs ' . $une_colonne["Field"] . ' avec un control de validite : ' . $une_colonne["explications"] . ' --&gt;
                    &lt;div class="form-group col-sm-6"&gt;
                        &lt;label &gt;' . $une_colonne["referenced_table"]["table_name"] . '&lt;/label&gt;
                        &lt;select [ngClass]="{ \'is-invalid\': submitted && f.' . $une_colonne["Field"] . '.errors }" class="form-select" formControlName="' . $une_colonne["Field"] . '"&gt;
                            &lt;option value=""&gt;Sélectionnez un(e) ' . $une_colonne["referenced_table"]["table_name"] . '&lt;/option&gt;
                            &lt;option [value]="one_' . $une_colonne["referenced_table"]["table_name"] . '.' . $une_colonne["referenced_table"]["cle_primaire"]["Field"] . '" *ngFor="let one_' . $une_colonne["referenced_table"]["table_name"] . ' of form_details.les_' . $une_colonne["referenced_table"]["table_name"] . 's"&gt;{{"' . $une_colonne["referenced_table"]["table_name"] . ' N°"+one_' . $une_colonne["referenced_table"]["table_name"] . '.' . $une_colonne["referenced_table"]["cle_primaire"]["Field"] . '}}&lt;/option&gt;
                        &lt;/select&gt;
                        &lt;div *ngIf="submitted && f.' . $une_colonne["Field"] . '.errors" class="invalid-feedback"&gt;
                            &lt;div *ngIf="f.' . $une_colonne["Field"] . '.errors.required"&gt;ce champ est obligatoire&lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;';
                    break;

                default: // les autres types de champs
                    if ($une_colonne["Field"] == "created_at" && $une_colonne["Default"] == "CURRENT_TIMESTAMP") {
                    } else {
                        $keysValues[] = '
                        &lt;!-- champs ' . $une_colonne["Field"] . ' avec une gestion de la validité --&gt;
                        &lt;div class="form-group col-sm-6"&gt;
                        &lt;label >' . $une_colonne["Field"] . '&lt;/label&gt;
                        &lt;input class="form-control" type="' . $type . '"  formControlName="' . $une_colonne["Field"] . '"  placeholder="' . $une_colonne["Field"] . '"  [ngClass]="{ \'is-invalid\': submitted && f.' . $une_colonne["Field"] . '.errors }"/&gt;
                        &lt;div *ngIf="submitted && f.' . $une_colonne["Field"] . '.errors" class="invalid-feedback"&gt;
                        &lt;div *ngIf="f.' . $une_colonne["Field"] . '.errors.required"&gt; ' . $une_colonne["Field"] . ' est obligatoire &lt;/div&gt;
                        &lt;/div&gt;
                        &lt;/div&gt;';
                    }
                    break;
            }
        }
        $content = implode("\t\t", $keysValues);
        return <<<HTML
            &lt;form  [formGroup]="reactiveForm_add_{$this->table_name} " (ngSubmit)="onSubmit_add_{$this->table_name} ()" #form_add_{$this->table_name} ="ngForm" class="row"&gt;
                $content
            &lt;/form&gt;
            &lt;!-- vous pouvez valider votre formulaire n\'importe ou --&gt;
           
            &lt;div class="text-center m-2"&gt;
                &lt;button type="button" class="btn btn-primary m-2" [disabled]="loading_add_{$this->table_name} "
                    (click)="form_add_{$this->table_name} .ngSubmit.emit()">{{loading_add_{$this->table_name} ?"En cours ...":"Valider"}}&lt;/button&gt;
                &lt;button class="btn btn-secondary m-2" type="reset" (click)="onReset_add_{$this->table_name} ()"&gt;Vider&lt;/button&gt;
            &lt;/div&gt;
        HTML;
    }

    public function add_form_ts()
    {
        $keysValues = array();
        foreach ($this->table_descriptions["les_colonnes"] as $key => $une_colonne) {
            $formcontrol = "";
            if ($une_colonne["Key"] != 'PRI' && !($une_colonne["Field"] == "created_at" && $une_colonne["Default"] != "")) {
                if ($une_colonne["Null"] == "NO") {
                    $formcontrol = '' . $une_colonne["Field"] . ': ["", Validators.required]';
                } else {
                    $formcontrol = '' . $une_colonne["Field"] . ': [""]';
                }
                $keysValues[] = $formcontrol;
            }
        }
        $content = implode(",\n", $keysValues);
        return <<<HTML
                reactiveForm_add_{$this->table_name} !: FormGroup;
                submitted:boolean=false
                loading_add_{$this->table_name} :boolean=false
                form_details: any = {}
                loading_get_details_add_{$this->table_name}_form = false
                constructor(private formBuilder: FormBuilder,public api:ApiService) { }
        
                ngOnInit(): void {
                    this.get_details_add_{$this->table_name}_form()
                    this.init_form()
                }
                init_form() {
                    this.reactiveForm_add_{$this->table_name}  = this.formBuilder.group({
                        $content
                    });
                }
            
                // acces facile au champs de votre formulaire
                get f(): any { return this.reactiveForm_add_{$this->table_name} .controls; }
                // validation du formulaire
                onSubmit_add_{$this->table_name} () {
                    this.submitted = true;
                    console.log(this.reactiveForm_add_{$this->table_name} .value)
                    // stop here if form is invalid
                    if (this.reactiveForm_add_{$this->table_name} .invalid) {
                        return;
                    }
                    var {$this->table_name} =this.reactiveForm_add_{$this->table_name} .value
                    this.add_{$this->table_name} ({$this->table_name} )
                }
                // vider le formulaire
                onReset_add_{$this->table_name} () {
                    this.submitted = false;
                    this.reactiveForm_add_{$this->table_name} .reset();
                }
                add_{$this->table_name}({$this->table_name}: any) {
                        this.loading_add_{$this->table_name} = true;
                        this.api.taf_post("{$this->table_name}/add", {$this->table_name}, (reponse: any) => {
                        if (reponse.status) {
                            console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ", reponse);
                            this.onReset_add_{$this->table_name}()
                            alert("{$this->table_name} ajouté avec succés")
                        } else {
                            console.log("L\'opération sur la table {$this->table_name} a échoué. Réponse= ", reponse);
                            alert("L'opération a echoué")
                        }
                        this.loading_add_{$this->table_name} = false;
                    }, (error: any) => {
                        this.loading_add_{$this->table_name} = false;
                    })
                }
                get_details_add_{$this->table_name}_form() {
                    this.loading_get_details_add_{$this->table_name}_form = true;
                    this.api.taf_post("{$this->table_name}/get_form_details", {}, (reponse: any) => {
                        if (reponse.status) {
                            this.form_details = reponse.data
                            console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ", reponse);
                        } else {
                            console.log("L'opération sur la table {$this->table_name} a échoué. Réponse= ", reponse);
                            alert("L'opération a echoué")
                        }
                        this.loading_get_details_add_{$this->table_name}_form = false;
                    }, (error: any) => {
                        this.loading_get_details_add_{$this->table_name}_form = false;
                    })
                }
            HTML;
    }
    public function add()
    {
        return <<<HTML
        <div class="d-flex justify-content-between">
             <h3 class="mb-2 mt-3">Add</h4>
            <!-- Example single danger button -->
            <button type="button" class="btn btn-soft-light">
                <a href="add">
                Tester l'api
                </a>
            </button>
        </div>
           <p class="fs-5">
           Pour insérer une nouvelle entrée dans la table <span class="text-danger">{$this->table_name}</span>, la démarche est aisée. Dites adieu aux laborieux formulaires à concevoir manuellement, tout est orchestré automatiquement pour vous simplifier l'existence. <br>
           Il vous suffit de nous accorder votre confiance en copiant le code ci-dessous, puis de l'intégrer judicieusement dans votre projet. Le tour est joué.
            </p>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#tab_add_form_html" type="button" role="tab" aria-controls="home" aria-selected="true">Code HTML</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#tab_add_form_ts" type="button" role="tab" aria-controls="profile" aria-selected="false">Code TypeScript (ts)</button>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="tab_add_form_html" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="ace_html">
                        {$this->add_form()}
                    </div>
                </div>
                <div class="tab-pane" id="tab_add_form_ts" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="ace_js">
                        {$this->add_form_ts()}
                    </div>
                </div>
            </div>
        HTML;
    }

    function getParamsForEdit()
    {
        $ts_object = implode(",\n\t\t", $this->description);
        return <<<HTML
                <div id="json_edit" class="col-12">
                {
                    $ts_object
                }
                </div>
            HTML;
    }

    function edit()
    {
        return <<<HTML
        <div class="d-flex justify-content-between">
             <h3 class="mb-2 mt-3">Edit</h4>
            <!-- Example single danger button -->
            <button type="button" class="btn btn-soft-light">
                <a href="edit">
                Tester l'api
                </a>
            </button>
        </div>
           <p class="fs-5">
           L'action "éditer" vous offre la possibilité de façonner vos données au sein de la table <span class="text-danger">{$this->table_name}</span>. Grâce à la fonction ci-dessous, la métamorphose de lignes au sein de votre table devient une réalité palpable. <br>
           Cette fonction requiert un objet en paramètre, où chaque clé correspond harmonieusement aux attributs de la table {$this->table_name} que vous aspirez à ajuster. C'est là, une étape vers la personnalisation à votre guise.
            </p>           
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#tab_edit_form_html" type="button" role="tab" aria-controls="home" aria-selected="true">Code HTML</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#tab_edit_form_ts" type="button" role="tab" aria-controls="profile" aria-selected="false">Code TypeScript (ts)</button>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="tab_edit_form_html" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="ace_html">
                        {$this->edit_form_html()}
                    </div>
                </div>
                <div class="tab-pane" id="tab_edit_form_ts" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="ace_js">
                        {$this->edit_form_ts()}
                    </div>
                </div>
            </div>
        HTML;
    }
    public function edit_form_html()
    {
        $keysValues = array();
        foreach ($this->table_descriptions["les_colonnes"] as $key => $une_colonne) {
            $type = 'text';
            switch ($une_colonne["Key"]) {
                case 'PRI': // rien pou la cle primaire
                    break;
                case 'MUL': // un champs de type select pour les cles etraangeres
                    $keysValues[] = '
                    &lt;!-- champs ' . $une_colonne["Field"] . ' avec un control de validite : ' . $une_colonne["explications"] . ' --&gt;
                    &lt;div class="form-group col-sm-6"&gt;
                        &lt;label &gt;' . $une_colonne["referenced_table"]["table_name"] . '&lt;/label&gt;
                        &lt;select [ngClass]="{ \'is-invalid\': submitted && f.' . $une_colonne["Field"] . '.errors }" class="form-select" formControlName="' . $une_colonne["Field"] . '"&gt;
                            &lt;option value=""&gt;Sélectionnez un(e) ' . $une_colonne["referenced_table"]["table_name"] . '&lt;/option&gt;
                            &lt;option [value]="one_' . $une_colonne["referenced_table"]["table_name"] . '.' . $une_colonne["referenced_table"]["cle_primaire"]["Field"] . '" *ngFor="let one_' . $une_colonne["referenced_table"]["table_name"] . ' of form_details.les_' . $une_colonne["referenced_table"]["table_name"] . 's"&gt;{{"' . $une_colonne["referenced_table"]["table_name"] . ' N°"+one_' . $une_colonne["referenced_table"]["table_name"] . '.' . $une_colonne["referenced_table"]["cle_primaire"]["Field"] . '}}&lt;/option&gt;
                        &lt;/select&gt;
                        &lt;div *ngIf="submitted && f.' . $une_colonne["Field"] . '.errors" class="invalid-feedback"&gt;
                            &lt;div *ngIf="f.' . $une_colonne["Field"] . '.errors.required"&gt;ce champ est obligatoire&lt;/div&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;';
                    break;

                default: // les autres types de champs
                    if ($une_colonne["Field"] == "created_at" && $une_colonne["Default"] == "CURRENT_TIMESTAMP") {
                    } else {
                        $keysValues[] = '
                        &lt;!-- champs ' . $une_colonne["Field"] . ' avec une gestion de la validité --&gt;
                        &lt;div class="form-group col-sm-6"&gt;
                        &lt;label >' . $une_colonne["Field"] . '&lt;/label&gt;
                        &lt;input class="form-control" type="' . $type . '"  formControlName="' . $une_colonne["Field"] . '"  placeholder="' . $une_colonne["Field"] . '"  [ngClass]="{ \'is-invalid\': submitted && f.' . $une_colonne["Field"] . '.errors }"/&gt;
                        &lt;div *ngIf="submitted && f.' . $une_colonne["Field"] . '.errors" class="invalid-feedback"&gt;
                        &lt;div *ngIf="f.' . $une_colonne["Field"] . '.errors.required"&gt; ' . $une_colonne["Field"] . ' est obligatoire &lt;/div&gt;
                        &lt;/div&gt;
                        &lt;/div&gt;';
                    }
                    break;
            }
        }
        $content = implode("\t\t", $keysValues);
        return <<<HTML
            &lt;form  [formGroup]="reactiveForm_edit_{$this->table_name} " (ngSubmit)="onSubmit_edit_{$this->table_name} ()" #form_edit_{$this->table_name} ="ngForm" class="row"&gt;
                $content
            &lt;/form&gt;
            &lt;!-- vous pouvez valider votre formulaire n\'importe ou --&gt;
           
            &lt;div class="text-center m-2"&gt;
                &lt;button type="button" class="btn btn-primary m-2" [disabled]="loading_edit_{$this->table_name} "
                    (click)="form_edit_{$this->table_name} .ngSubmit.emit()">{{loading_edit_{$this->table_name} ?"En cours ...":"Valider"}}&lt;/button&gt;
                &lt;button class="btn btn-secondary m-2" type="reset" (click)="onReset_edit_{$this->table_name} ()"&gt;Vider&lt;/button&gt;
            &lt;/div&gt;
        HTML;
    }
    public function edit_form_ts()
    {
        $keysValues_update = array();
        foreach ($this->table_descriptions["les_colonnes"] as $key => $une_colonne) {
            $formcontrol = "";
            if ($une_colonne["Key"] != 'PRI' && !($une_colonne["Field"] == "created_at" && $une_colonne["Default"] != "")) {
                if ($une_colonne["Null"] == "NO") {
                    $formcontrol = '' . $une_colonne["Field"] . ': [' . $this->table_name . '_to_edit.' . $une_colonne["Field"] . ', Validators.required]';
                } else {
                    $formcontrol = '' . $une_colonne["Field"] . ': [' . $this->table_name . '_to_edit.' . $une_colonne["Field"] . ']';
                }
                $keysValues_update[] = $formcontrol;
            }
        }
        $content_update = implode(",\n\t", $keysValues_update);
        return <<<HTML
                <div class="row position-relative my-5">
                    <div id="edit_form_ts" class="col-12">
                        reactiveForm_edit_{$this->table_name} !: FormGroup;
                        submitted: boolean = false
                        loading_edit_{$this->table_name}: boolean = false
                        @Input()
                        {$this->table_name}_to_edit: any = {}
                        @Output()
                        cb_edit_{$this->table_name} = new EventEmitter()
                        form_details: any = {}
                        loading_get_details_add_{$this->table_name}_form = false
                        constructor(private formBuilder: FormBuilder, public api: ApiService) {

                        }
                        ngOnInit(): void {
                            this.get_details_add_{$this->table_name}_form()
                            this.update_form(this.{$this->table_name}_to_edit)
                        }
                        // mise à jour du formulaire
                        update_form({$this->table_name}_to_edit: any) {
                            this.reactiveForm_edit_{$this->table_name} = this.formBuilder.group({
                                {$content_update}
                            });
                        }

                        // acces facile au champs de votre formulaire
                        get f(): any { return this.reactiveForm_edit_{$this->table_name}.controls; }
                        // validation du formulaire
                        onSubmit_edit_{$this->table_name}() {
                            this.submitted = true;
                            console.log(this.reactiveForm_edit_{$this->table_name}.value)
                            // stop here if form is invalid
                            if (this.reactiveForm_edit_{$this->table_name}.invalid) {
                                return;
                            }
                            var {$this->table_name} = this.reactiveForm_edit_{$this->table_name}.value
                            this.edit_{$this->table_name}({
                                condition: JSON.stringify({ id_{$this->table_name}: this.{$this->table_name}_to_edit.id_{$this->table_name} }),
                                data: JSON.stringify({$this->table_name})
                            })
                        }
                        // vider le formulaire
                        onReset_edit_{$this->table_name}() {
                            this.submitted = false;
                            this.reactiveForm_edit_{$this->table_name}.reset();
                        }
                        edit_{$this->table_name}({$this->table_name}: any) {
                            this.loading_edit_{$this->table_name} = true;
                            this.api.taf_post("{$this->table_name}/edit", {$this->table_name}, (reponse: any) => {
                                if (reponse.status) {
                                    this.cb_edit_{$this->table_name}.emit({
                                        new_data: JSON.parse({$this->table_name}.data)
                                    })
                                    console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ", reponse);
                                    this.onReset_edit_{$this->table_name}()
                                    alert("Opération effectuée avec succés sur la table {$this->table_name}")
                                } else {
                                    console.log("L'opération sur la table {$this->table_name} a échoué. Réponse= ", reponse);
                                    alert("L'opération a echoué")
                                }
                                this.loading_edit_{$this->table_name} = false;
                            }, (error: any) => {
                                this.loading_edit_{$this->table_name} = false;
                            })
                        }
                        get_details_add_{$this->table_name}_form() {
                            this.loading_get_details_add_{$this->table_name}_form = true;
                            this.api.taf_post("{$this->table_name}/get_form_details", {}, (reponse: any) => {
                                if (reponse.status) {
                                    this.form_details = reponse.data
                                    console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ", reponse);
                                } else {
                                    console.log("L'opération sur la table {$this->table_name} a échoué. Réponse= ", reponse);
                                    alert("L'opération a echoué")
                                }
                                this.loading_get_details_add_{$this->table_name}_form = false;
                            }, (error: any) => {
                                this.loading_get_details_add_{$this->table_name}_form = false;
                            })
                        }
                    </div>
                </div>
            HTML;
    }

    function getParamsForDelete()
    {
        $keysValues = array();
        $keysValues[] = "id_......:' ...... (primary key, obligatoire)'";
        $ts_object = implode(",\n\t\t", $keysValues);
        return <<<HTML
            <div id="json_delete" class="col-12">
            {
                $ts_object
            }
            </div>
        HTML;
    }

    function delete()
    {
        return <<<HTML
                <div class="d-flex justify-content-between">
                     <h3 class="mb-2 mt-3">Delete</h4>
                    <!-- Example single danger button -->
                    <button type="button" class="btn btn-soft-light">
                        <a href="delete">
                        Tester l'api
                        </a>
                    </button>
                </div>
                   <p class="fs-5">
                   L'action "supprimer" vous octroie le pouvoir de retrancher des données de la table <span class="text-danger">{$this->table_name}</span>. Grâce à la fonction ci-dessous, l'élimination de lignes devient une opération à portée de main. <br>
                   Cette fonction requiert un objet en paramètre, où chaque clé s'accorde harmonieusement aux attributs de la table {$this->table_name}. Les valeurs associées définissent avec précision les conditions pour la suppression d'une ligne. Une démarche réfléchie vers la gestion ciblée de vos données.
                    </p>           
                <div class="ace_js">
                    /*
                    Suppression de l'enregistresement dont l'id_{$this->table_name}=1
                    this.delete_{$this->table_name}({id_{$this->table_name}:1})
                    */
                    delete_{$this->table_name} ({$this->table_name} : any){
                        this.loading_delete_{$this->table_name} = true;
                        this.api.taf_post("{$this->table_name}/delete", {$this->table_name},(reponse: any)=>{
                            //when success
                            if(reponse.status){
                                console.log("Opération effectuée avec succés sur la table {$this->table_name} . Réponse = ",reponse)
                                alert("Opération effectuée avec succés")
                            }else{
                                console.log("L\'opération sur la table {$this->table_name}  a échoué. Réponse = ",reponse)
                                alert("L'opération a échouée")
                            }
                            this.loading_delete_{$this->table_name} = false;
                        },
                        (error: any)=>{
                            //when error
                            console.log("Erreur inconnue! ",error)
                            this.loading_delete_{$this->table_name} = false;
                        })
                    }
                </div>
            HTML;
        }
}
