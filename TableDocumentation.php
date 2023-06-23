<?php

namespace Taf;

use PDO;

class TableDocumentation extends TafConfig
{
    public $url = "";
    public $description = [];
    public $table_name = "";
    public function __construct($table_name)
    {
        $this->table_name = $table_name;
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
            <a class="fs-2" data-bs-toggle="collapse" href="#docs_get_{$this->table_name}" role="button" aria-expanded="false" aria-controls="docs_get_{$this->table_name}">
            Get
            </a>
            <!-- Example single danger button -->
            <div class="btn-group d-inline">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="get">Tester l'api</a></li>
                </ul>
            </div>
        </div>
        <div class="collapse" id="docs_get_{$this->table_name}">
            <div class="row my-5">
                <p class="col-12 text-justify fs-4">
                    L'action get permet d'obtenir des données de votre table <span class="text-danger">{$this->table_name}</span>, grâce 
                    à la fonction ci-dessous vous pouvez récupérer une donnée spécifique ou toutes les données.
                </p>
            </div>
            <div class="row position-relative my-5">
                <div id="get_exemple" class="col-12">
                    get_$this->table_name(){
                    this.loading_edit_{$this->table_name} = true;
                    this.api.taf_get("{$this->table_name}/get").subscribe((reponse:any)=>{
                        //when success
                        this.loading_edit_{$this->table_name} = false;
                        if(reponse.status){
                            console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ",reponse);
                        }else{
                            console.log("L\'opération sur la table {$this->table_name} a échoué. Réponse= ",reponse);
                        }
                    },
                    (error:any)=>{
                        //when error
                        this.loading_edit_{$this->table_name} = false;
                        console.log("Erreur inconnue! ",error);
                    })
                    }
                </div>
            </div>
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
        foreach ($this->description as $key => $value) {
            $cle = $value;
            $type = 'text';

            $keysValues[] = '
            &lt;!-- ' . $cle . ' field avec un control de validite --&gt;
            &lt;div class="form-group col-sm-6"&gt;
              &lt;label >' . $cle . '&lt;/label&gt;
              &lt;input class="form-control" type="' . $type . '"  formControlName="' . $cle . '"  placeholder="' . $cle . '"  [ngClass]="{ \'is-invalid\': submitted && f.' . $cle . '.errors }"/&gt;
              &lt;div *ngIf="submitted && f.' . $cle . '.errors" class="invalid-feedback"&gt;
                  &lt;div *ngIf="f.' . $cle . '.errors.required"&gt; ' . $cle . ' est obligatoire &lt;/div&gt;
              &lt;/div&gt;
            &lt;/div&gt;';
        }
        $content = implode("\t\t", $keysValues);
        return <<<HTML
        <div class="row position-relative my-5">
          <div id="add_form" class="col-12">
            &lt;form  [formGroup]="reactiveForm_add_{$this->table_name} " (ngSubmit)="onSubmit_add_{$this->table_name} ()" #form_add_{$this->table_name} ="ngForm" class="row"&gt;
                $content
            &lt;/form&gt;
            &lt;!-- vous pouvez valider votre formulaire n\'importe ou --&gt;
           
            &lt;div class="text-center m-2"&gt;
                &lt;button type="button" class="btn btn-primary m-2" [disabled]="loading_add_{$this->table_name} "
                    (click)="form_add_{$this->table_name} .ngSubmit.emit()">{{loading_add_{$this->table_name} ?"En cours ...":"Valider"}}&lt;/button&gt;
                &lt;button class="btn btn-secondary m-2" type="reset" (click)="onReset_add_{$this->table_name} ()"&gt;Vider&lt;/button&gt;
            &lt;/div&gt;
          </div>
        </div>
        HTML;
    }

    public function add_form_ts()
    {
        $keysValues = array();
        foreach ($this->description as $key => $value) {
            $cle = $value;
            $type = 'text';

            $keysValues[] = '
            ' . $cle . ': ["", Validators.required]';
        }
        $content = implode(",", $keysValues);
        return <<<HTML
                <div class="row position-relative my-5">
                    <div id="add_form_ts" class="col-12">
                        reactiveForm_add_{$this->table_name} !: FormGroup;
                        submitted:boolean=false
                        loading_add_{$this->table_name} :boolean=false
                        constructor(private formBuilder: FormBuilder,public api:ApiService, private http:HttpClient) { }
                
                        ngOnInit(): void {
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
                    </div>
                </div>
            HTML;
    }
    public function add()
    {
        return <<<HTML
        <div class="d-flex justify-content-between">
            <a class="fs-2" data-bs-toggle="collapse" href="#docs_add_{$this->table_name}" role="button" aria-expanded="false" aria-controls="docs_add_{$this->table_name}">
            Add
            </a>
            <!-- Example single danger button -->
            <div class="btn-group d-inline">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="add">Tester l'api</a></li>
                </ul>
            </div>
        </div>
            <div class="collapse" id="docs_add_{$this->table_name}">
                <div class="row ">
                    <p class="col-12 text-justify fs-4">
                        L'action add permet d'ajouter des données dans votre table <span class="text-danger">{$this->table_name}</span>, grâce 
                        à la fonction ci-dessous vous pouvez ajouter des lignes dans votre table. <br>
                        Cette fonction prend en paramètre un objet dont les clés correspondent aux attributs de la table {$this->table_name} dont 
                        vous souhaitez affecter une nouvelle valeur.
                    </p>
                    {$this->getParamsForAdd()}
                </div>
                <div class="row position-relative">
                    <div id="add_exemple" class="col-12">
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
                    </div>
                </div>
                <div class="row">
                    <p class="col-12 text-justify fs-4">
                    Pour ajouter ainsi une nouvelle ligne dans la table <span class="text-danger">{$this->table_name}</span>, c'est simple.
                    Fini les longs formulaire à coder à la main, tout est généré automatiquement pour vous simplifiez la vie. <br>
                    Vous n'avez qu'à faire nous faire confiance en copiant le code ci-dessous et le mettre au bon endroit dans votre projet et le tour est joué.
                    </p>
                    {$this->add_form()}
                    <h6>TS</h6>
                    {$this->add_form_ts()}
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
            <a class="fs-2" data-bs-toggle="collapse" href="#docs_edit_{$this->table_name}" role="button" aria-expanded="false" aria-controls="docs_edit_{$this->table_name}">
            Edit
            </a>
            <!-- Example single danger button -->
            <div class="btn-group d-inline">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="edit">Tester l'api</a></li>
                </ul>
            </div>
        </div>
        <div class="collapse" id="docs_edit_{$this->table_name}">
            <div class="row my-5">
                <p class="col-12 text-justify fs-4">
                L'action edit permet de modifier des données dans votre table <span class="text-danger">{$this->table_name}</span>, grâce 
                à la fonction ci-dessous vous pouvez modifier des lignes de votre table. <br>
                Cette fonction prend en paramètre un objet dont les clés correspondent aux attributs de la table {$this->table_name} dont vous
                souhaitez modifier.
                </p>
                {$this->getParamsForEdit()}
            </div>
            <div class="row position-relative my-5">
                <div id="edit_exemple" class="col-12">
                    edit_{$this->table_name}({$this->table_name}: any) {
                        this.loading_edit_{$this->table_name} = true;
                        this.api.taf_post("{$this->table_name}/edit", {$this->table_name}, (reponse: any) => {
                            if (reponse.status) {
                                console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ", reponse);
                                this.onReset_edit_{$this->table_name}()
                                alert("Opération éffectuée ajouté avec succés")
                            } else {
                                console.log("L\'opération sur la table {$this->table_name} a échoué. Réponse= ", reponse);
                                alert("L'opération a echoué")
                            }
                            this.loading_edit_{$this->table_name} = false;
                        }, (error: any) => {
                            this.loading_edit_{$this->table_name} = false;
                        })
                    }
                </div>
            </div>
            <div class="row">
                <p class="col-12 text-justify fs-4">Pour modifier</p>
                {$this->edit_form_html()}
                <h6>TS</h6>
                {$this->edit_form_ts()}
            </div>
        </div>
        HTML;
    }
    public function edit_form_html()
    {
        $keysValues = array();
        foreach ($this->description as $key => $value) {
            $cle = $value;
            $type = 'text';

            $keysValues[] = '
            &lt;!-- ' . $cle . ' field avec un control de validite --&gt;
            &lt;div class="form-group col-sm-6"&gt;
              &lt;label >' . $cle . '&lt;/label&gt;
              &lt;input class="form-control" type="' . $type . '"  formControlName="' . $cle . '"  placeholder="' . $cle . '" class="form-control" [ngClass]="{ \'is-invalid\': submitted && f.' . $cle . '.errors }"/&gt;
              &lt;div *ngIf="submitted && f.' . $cle . '.errors" class="invalid-feedback"&gt;
                  &lt;div *ngIf="f.' . $cle . '.errors.required"&gt; ' . $cle . ' est obligatoire &lt;/div&gt;
              &lt;/div&gt;
            &lt;/div&gt;';
        }
        $content = implode("\t\t", $keysValues);
        return <<<HTML
        <div class="row position-relative my-5">
          <div id="edit_form" class="col-12">
            &lt;form  [formGroup]="reactiveForm_edit_{$this->table_name} " (ngSubmit)="onSubmit_edit_{$this->table_name} ()" #form_edit_{$this->table_name} ="ngForm" class="row"&gt;
                $content
            &lt;/form&gt;
            &lt;!-- vous pouvez valider votre formulaire n\'importe ou --&gt;
           
            &lt;div class="text-center m-2"&gt;
                &lt;button type="button" class="btn btn-primary m-2" [disabled]="loading_edit_{$this->table_name} "
                    (click)="form_edit_{$this->table_name} .ngSubmit.emit()">{{loading_edit_{$this->table_name} ?"En cours ...":"Valider"}}&lt;/button&gt;
                &lt;button class="btn btn-secondary m-2" type="reset" (click)="onReset_edit_{$this->table_name} ()"&gt;Vider&lt;/button&gt;
            &lt;/div&gt;
          </div>
        </div>
        HTML;
    }
    public function edit_form_ts()
    {
        $keysValues = array();
        foreach ($this->description as $key => $value) {
            $cle = $value;
            $type = 'text';

            $keysValues[] = '
            ' . $cle . ': ["", Validators.required]';
            $keysValues_update[] = '
            ' . $cle . ': [' . $this->table_name . '_to_edit.' . $cle . ', Validators.required]';
        }
        $content = implode(",", $keysValues);
        $content_update = implode(",", $keysValues_update);
        return <<<HTML
                <div class="row position-relative my-5">
                    <div id="edit_form_ts" class="col-12">
                        reactiveForm_edit_{$this->table_name} !: FormGroup;
                        submitted: boolean = false
                        loading_edit_{$this->table_name}: boolean = false
                        id_{$this->table_name}:number=0;
                        {$this->table_name}_to_edit: any = {}
                        constructor(private formBuilder: FormBuilder, public api: ApiService, private http: HttpClient,private a_route:ActivatedRoute) { 
                            // récupération des paramètres depuis l'URL
                            a_route.params.subscribe((params:any)=>{
                                if (params["id_{$this->table_name}"]) {// si le prametre id_{$this->table_name} est présent sur l'URL
                                    this.id_{$this->table_name}=params["id_{$this->table_name}"]
                                    this.get_{$this->table_name}(this.id_{$this->table_name})
                                } else {// en cas d'absence
                                    alert("L'URL est incorrect. Il manque l'information 'id_{$this->table_name}'")
                                }
                            })
                        }
                        ngOnInit(): void {
                            this.init_form()
                        }
                        init_form() {
                            this.reactiveForm_edit_{$this->table_name}  = this.formBuilder.group({
                                $content
                            });
                        }
                        // récupération des details de l'élément à modifier
                        get_{$this->table_name}(id_{$this->table_name}: number) {
                            this.api.taf_get("{$this->table_name}/get?id_{$this->table_name}=" + id_{$this->table_name}, (reponse: any) => {
                                //when success
                                if (reponse.status) {
                                    console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ", reponse);
                                    if (reponse.data.length>0) {
                                        this.{$this->table_name}_to_edit = reponse.data[0]
                                        this.update_form(this.{$this->table_name}_to_edit)
                                    } else {
                                        alert("Détails de l'élément à modifier introuvable")
                                    }
                                } else {
                                    console.log("L\'opération sur la table {$this->table_name} a échoué. Réponse= ", reponse);
                                }
                            },
                            (error: any) => {
                                //when error
                                console.log("Erreur inconnue! ", error);
                            })
                        }
                        // mise à jour du formulaire
                        update_form({$this->table_name}_to_edit:any) {
                            this.reactiveForm_edit_{$this->table_name} = this.formBuilder.group({
                                $content_update
                            });
                        }
                    
                        // acces facile au champs de votre formulaire
                        get f(): any { return this.reactiveForm_edit_{$this->table_name} .controls; }
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
                            condition:JSON.stringify({id_{$this->table_name}:this.id_{$this->table_name}}),
                            data:JSON.stringify({$this->table_name})
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
                                    console.log("Opération effectuée avec succés sur la table {$this->table_name}. Réponse= ", reponse);
                                    this.onReset_edit_{$this->table_name}()
                                    alert("Opération effectuée avec succés sur la table {$this->table_name}")
                                } else {
                                    console.log("L\'opération sur la table {$this->table_name} a échoué. Réponse= ", reponse);
                                    alert("L'opération a echoué")
                                }
                                this.loading_edit_{$this->table_name} = false;
                            }, (error: any) => {
                                this.loading_edit_{$this->table_name} = false;
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
                <a class="fs-2" data-bs-toggle="collapse" href="#docs_delete_{$this->table_name}" role="button" aria-expanded="false" aria-controls="docs_delete_{$this->table_name}">
                Delete
                </a>
                <!-- Example single danger button -->
                <div class="btn-group d-inline">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="delete">Tester l'api</a></li>
                    </ul>
                </div>
            </div>
            <div class="collapse" id="docs_delete_{$this->table_name}">
                <div class="row my-5">
                    <p class="col-12 text-justify fs-4">
                    L'action delete permet de supprimer des données de votre table <span class="text-danger">{$this->table_name}</span>, grâce 
                    à la fonction ci-dessous vous pouvez supprimer des lignes de votre table. <br>
                    Cette fonction prend en paramètre un objet dont les clés correspondent aux attributs de la table {$this->table_name} et dont 
                    les valeurs permettent de définir la condition de suppression d'une ligne de la table.
                    </p>
                    {$this->getParamsForDelete()}
                </div>
                <div class="row position-relative my-5">
                <div id="delete_exemple" class="col-12">
                    delete_{$this->table_name} ({$this->table_name} : any){
                        this.loading_delete_{$this->table_name} = true;
                        //transformation des parametres à envoyer
                        let formdata = new FormData()
                        for (const key in {$this->table_name} ) {
                            formdata.append(key, {$this->table_name} [key])
                        }
                    
                        this.api.taf_post("{$this->table_name}/delete", {$this->table_name},(reponse: any)=>{
                            //when success
                            if(reponse.status){
                                console.log("Opération effectuée avec succés sur la table {$this->table_name} . Réponse = ",reponse)
                            }else{
                                console.log("L\'opération sur la table {$this->table_name}  a échoué. Réponse = ",reponse)
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
                </div>
            </div>
       HTML;
    }
}
