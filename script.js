// define the tree-item component
Vue.component("tree-item", {
    template: "#item-template",
    props: {
        item: Object
    },
    data: function() {
        return {
        isOpen: false
        };
    },
    computed: {
        isFolder: function() {
        return this.item.children && this.item.children.length;
        }
    },
    methods: {
        toggle: function() {
            if (this.isFolder) {
                if (this.isOpen){
                    this._props.item.children=[{"name":"Загрузка.."}];
                } else{
                    apiRequest('./api/creators.php/'+this._props.item.name,'GET').then(data =>{
                        if(data.error==0) {
                            this._props.item.children = data.content;
                        }else{
                            errorWindow.Show(data.content);
                        }
                    }).catch(err => {
                        errorWindow.Show(err);
                    });                
                }
                this.isOpen = !this.isOpen;
                console.log(this)
            } else{
                Books.load(this._props.item.id);
            }
        },
        editEntry:function(id){
            creatorFrom.openForm(id);            
        },
        deleteEntry:function(id){
            if(confirm("Удалить автора?")){
                apiRequest('./api/creator.php/'+id,'DELETE').then(data =>{
                if(data.error==0) {
                        console.log(data.content);
                }else{
                        errorWindow.Show(data.content);
                    }
                }).catch(err => {
                    errorWindow.Show(err);
                });
            }        
        }        
    }
});
Vue.component("modal", {
    props: ['addstyle'], 
    template: "#modal-template"
});
Vue.component('treeselect', VueTreeselect.Treeselect)


var loginForm = new Vue({
    el: "#loginForm",
    data: {
        showLoginWindow: false,
        login: '',
        password:'',
        userManagement: false,
        editBook:false,
        editDirectory:false,
        editCreator:false,
        deleteBook:false,
        isLogined:false,
        name:"Вход в систему"
    },methods:{
        openLoginFrom:function() {
            this.showLoginWindow = true;
        },
        loginBtn: function() {
            console.log('login');
            this.showLoginWindow = false;
            let jsonData = {"actons":"login","module":"User","login":this.login,"password":this.password};
            this.password='';
            apiRequest('./api/user.php','POST',jsonData).then(data =>{
                if(data.error==0) {
                    console.log(data.content);
                    this.setAccessToElement(data.content.rights);
                    this.name = data.content.name;
                }else{
                    errorWindow.Show(data.content);
                }
            }).catch(err => {
                errorWindow.Show(err);
            }); 
        },
        closeBtn: function() {
            this.showLoginWindow = false;
        },
        loadcurrentState: function(){
             let jsonData = {"actons":"getCurentLogin"};
              apiRequest('./api/user.php','POST',jsonData).then(data =>{
                if(data.error==0) {
                    console.log(data.content);
                    if (data.content.rights != null){
                        this.setAccessToElement(data.content.rights);
                        this.name = data.content.name;
                    }
                }else{
                    errorWindow.Show(data.content);
                } 
                  
            }).catch(err => {
                errorWindow.Show(err);
            });           
        },
        logout:function(){
            let jsonData = {"actons":"lodout"};
            apiRequest('./api/user.php','POST',jsonData).then(data =>{
                 if(data.error==0) {
                    console.log(data.content);
                    this.setAccessToElement([])
                    loginForm.isLogined = false;
                }else{
                    errorWindow.Show(data.content);
                } 
                  
            }).catch(err => {
                errorWindow.Show(err);
            });             
        },
        setAccessToElement: function (rights){
            loginForm.userManagement = false;
            loginForm.editBook = false;
            loginForm.editDirectory = false;
            loginForm.editCreator = false;
            loginForm.deleteBook = false;
            loginForm.isLogined = true;
            loginForm.name = "Вход в систему";
            rights.forEach(function ( element ) {
                switch (element) {
                case 'userManagement':
                    loginForm.userManagement = true;
                    break;
                case 'editBook':
                    loginForm.editBook =true;
                    break;
                case 'editDirectory':
                    loginForm.editDirectory =true;
                    break;
                case 'editCreator':
                    loginForm.editCreator = true;
                    break;   
                case 'deleteBook':
                    loginForm.deleteBook = true;
                    break;                     
                }
            })
        }
        
        
    },
    created:function () {
        this.loadcurrentState();
    }
});

var topPanel = new Vue({
    el: "#topPanel",
    methods:{
        openLoginFrom:function() {
            loginForm.openLoginFrom();
        }, 
        openUsersForm:function() {
            usersForm.openFrom();
        },
        opendirectoryEditor:function(dir) {
            directoryList.openFrom(dir);
        },
        logout:function() {
            loginForm.logout();
        },
        
    }
    
});


var bookForm = new Vue({
    el: "#bookForm",
    data: {
        showWindow: false,
        book:{},
        Allgenres:[],
        AllLanguage:[],
        AllTypes:[],
        AviableBooks:[],
        AllPublishers:[]
    },methods:{
        openBookFrom:function(bookId,creatorId=null) {
            apiRequest('./api/book.php/'+bookId,'GET').then(data =>{
            if(data.error==0) {
                    this.book=data.content.book;
                    this.Allgenres = data.content.Allgenres;
                    this.AllLanguage = data.content.AllLanguage;
                    this.AllTypes = data.content.AllTypes;
                    this.AviableBooks = data.content.AviableBooks;
                    this.AllPublishers = data.content.AllPublishers;
                    this.showWindow = true;
                    if(creatorId != null){
                        this.book.creatorId = creatorId;
                    }                    
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        saveBtn:function() {
            console.log(this.book.genres);
//            let jsonData = {"actons":"save","module":"Book","book":this.book};
            apiRequest('./api/book.php/','PUT',this.book).then(data =>{
            if(data.error==0) {
                    this.showWindow = false;
                    Books.load(this.book.creatorId);
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            });
        },        
        closeBtn: function() {
            this.showWindow = false;
        },
        addCopy:function() {
            result = prompt('Введите номер акта', 'Номер акта');
            if(result != null){
                apiRequest('./api/inventory.php/','PUT',{"bookId":this.book.bookId,"act":result}).then(data =>{
                if(data.error==0) {
                        this.openBookFrom(this.book.bookId);
                    }else{
                        console.log(data.content)
                    }
                }).catch(err => {
                    console.log('Fetch Error', err);
                });
            }
        },
        handingOut:function(inventoryId) {
            handingОutForm.openForm(inventoryId);
        },
        handingIn:function(inventoryId) {
            if(confirm("Принять экземпляр?")){
                apiRequest('./api/inventory.php/'+inventoryId+'/In','POST').then(data =>{
                if(data.error==0) {
                        this.openBookFrom(this.book.bookId);
                    }else{
                        console.log(data.content)
                    }
                }).catch(err => {
                    console.log('Fetch Error', err);
                });
            }
        },       
        deleteCopy:function(inventoryId) {
            result = prompt('Введите номер акта', 'Номер акта');
            if(result != null ){
                apiRequest('./api/inventory.php/'+inventoryId,'DELETE').then(data =>{
                if(data.error==0) {
                        this.openBookFrom(this.book.bookId);
                    }else{
                        console.log(data.content)
                    }
                }).catch(err => {
                    console.log('Fetch Error', err);
                });
            }
        }
    }
});

var usersForm = new Vue({
    el: "#usersForm",
    data: {
        showWindow: false,
        users: []
    },
    methods: {
        openFrom:function() {
            apiRequest('./api/users.php','GET').then(data =>{
            if(data.error==0) {
                    this.users=data.content;
                    this.showWindow = true;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        deleteUser:function(userId) {
            if(confirm("Удалить пользователя?")){
                apiRequest('./api/user.php/'+userId,'DELETE').then(data =>{
                if(data.error==0) {
                        this.openFrom();
                    }else{
                        console.log(data.content)
                    }
                }).catch(err => {
                    console.log('Fetch Error', err);
                }); 
            }
        },        
        openUser:function(userId) {
            userForm.openForm(userId);       
        }
    }
    
})
var userForm = new Vue({
    el: "#userForm",
    data: {
        showWindow: false,
        user:{},
        password:"",
        passwordInvalide:false,
        roles:[]
    },methods:{
        openForm:function(userId) {
            apiRequest('./api/user.php/'+userId,'GET').then(data =>{
            if(data.error==0) {
                    this.user=data.content.user;
                    this.roles=data.content.roles;
                    this.showWindow = true;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        saveBtn:function() {
            if (this.password == this.user.password){    
                this.passwordInvalide=false;
               
                apiRequest('./api/user.php/'+this.user.userId,'PUT',this.user).then(data =>{
                if(data.error==0) {
                        this.showWindow = false;
                    }else{
                        console.log(data.content)
                    }
                }).catch(err => {
                    console.log('Fetch Error', err);
                });
            }else{
                this.passwordInvalide=true;
            }
        },        
        closeBtn: function() {
            this.showWindow = false;
        }        
    }
});

var creators = new Vue({
    el: "#creators",
    data: {
          treeData:   [],
          editCreator: false
    },
    methods: {
        loadAlphabet:function () {
            let jsonData = {"actons":"getFirstLetter","module":"Search"};
            apiRequest('./api/creators.php/','GET').then(data =>{
                if(data.error==0) {
                    data.content.forEach(item => {
                        this.treeData.push({name:item,children:[{"name":"Загрузка.."}]});
                    });
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        editEntry:function(id) {
            creatorFrom.openForm(id);  
        }
    },
    created:function () {
        this.loadAlphabet();
    }
});
var Books = new Vue({
    el: '#books',
    data:{
        books:[],
        creatorId:0
    },
    methods:{
        load:function (creatorId) {
            apiRequest('./api/books.php/'+creatorId,'GET').then(data =>{
                if(data.error==0) {
                    this.books=data.content;
                    this.creatorId = creatorId;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            });
        },
        deleteBook:function (bookId) {
            if(confirm("Удалить книгу?")){
                apiRequest('./api/book.php/'+bookId,'DELETE').then(data =>{
                    if(data.error==0) {
                        this.load(this.creatorId);
                    }else{
                        errorWindow.Show(data.content);
                    }
                }).catch(err => {
                    errorWindow.Show(err);
                    
                });
            }

        },
        reserveBook:function (bookId) {
            if(confirm("Зарезервировать книгу?")){
                apiRequest('./api/book.php/'+bookId+"/reserv/",'POST').then(data =>{
                    if(data.error==0) {
                        errorWindow.Show(data.content);
                    }else{
                        errorWindow.Show(data.content);
                    }
                }).catch(err => {
                    errorWindow.Show(err);
                });
            }

        },        
        openBookFrom:function (bookId) {
            bookForm.openBookFrom(bookId,this.creatorId);
        }
    }
    
})



var handingОutForm = new Vue({
    el: "#handingОutForm",
    data: {
        showWindow: false,
        selectedUser: null,
        users:{},
        selectedDate: null,
        inventoryId: null
    },methods:{
        openForm:function(inventoryId) {
            apiRequest('./api/users.php/tree','GET').then(data =>{
            if(data.error==0) {
                    this.inventoryId = inventoryId;
                    this.selectedDate = addDays(Date(),7);
                    this.users=data.content;
                    this.showWindow = true;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            });             
            this.showWindow = true;
        },
        saveBtn:function() {
            this.passwordInvalide=false;
            apiRequest('./api/inventory.php/'+this.inventoryId+'/out/','POST',{"selectedDate":this.selectedDate.getTime() / 1000,"selectedUser":this.selectedUser}).then(data =>{
            if(data.error==0) {
                    bookForm.openBookFrom(bookForm.book.bookId);
                    this.showWindow = false;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            });
        },        
        closeBtn: function() {
            this.showWindow = false;
        }        
    }
});


var directoryList = new Vue({
    el: "#directoryList",
    data: {
        showWindow: false,
        data:{},
        dir:""
    },
    methods:{
        openFrom:function (dir) {
            apiRequest('./api/directorylist.php/'+dir,'GET').then(data =>{
                if(data.error==0) {
                    this.dir = dir;
                    this.data=data.content;
                    this.showWindow = true;
                }else{
                    errorWindow.Show(data.content);
                }
            }).catch(err => {
                errorWindow.Show(err);
            });
        },
        openEntry:function(id) {
            directoryEntry.openForm(id,this.dir);       
        },        
        deleteEntry: function(id){
             if(confirm("Удалить?")){
                alert()
                apiRequest('./api/directory.php/'+this.dir+'/'+id,'DELETE').then(data =>{
                    if(data.error==0) {
                        this.openFrom(this.dir);
                    }else{
                        errorWindow.Show(data.content);
                    }
                }).catch(err => {
                    errorWindow.Show(err);
                });
            }           
        },
        CloseWindow: function() {
            this.showWindow = false;
        }        
    }
});

var directoryEntry = new Vue({
    el: "#directoryEntry",
    data: {
        showWindow: false,
        directory:{},
        dir:""
    },methods:{
        openForm:function(id,dir) {
            this.dir = dir;
            apiRequest('./api/directory.php/'+this.dir+'/'+id,'GET').then(data =>{
            if(data.error==0) {
                    this.directory=data.content.directory;
                    this.showWindow = true;
                }else{
                    errorWindow.Show(data.content);
                    console.log(data.content)
                }
            }).catch(err => {
                errorWindow.Show(err);
            }); 
        },
        saveBtn:function() {           
            apiRequest('./api/directory.php/'+this.dir+'/'+this.id,'PUT',this.directory).then(data =>{
            if(data.error==0) {
                    this.showWindow = false;
                    directoryList.openFrom(this.dir);
                }else{
                    errorWindow.Show(data.content);
                }
            }).catch(err => {
                errorWindow.Show(err);
            });
        },        
        closeBtn: function() {
            this.showWindow = false;
        }        
    }
});
var creatorFrom = new Vue({
    el: "#creatorFrom",
    data: {
        showWindow: false,
        directory:{},
    },
    methods:{
        openForm:function(id){
            if (id == null){
                this.directory={};
                this.showWindow = true;
            }else{
                this.loadData(id);
            }
        },
        loadData:function(id) {
            apiRequest('./api/creator.php/'+id,'GET').then(data =>{
                if(data.error==0) {
                    this.directory=data.content.directory;
                    this.showWindow = true;
                }else{
                    errorWindow.Show(data.content);
                }
            }).catch(err => {
                errorWindow.Show(err);
            }); 
        },
        saveBtn:function() {           
            apiRequest('./api/creator.php/'+this.id,'PUT',this.directory).then(data =>{
            if(data.error==0) {
                    this.showWindow = false;
                }else{
                    errorWindow.Show(data.content);
                }
            }).catch(err => {
                errorWindow.Show(err);
            });
        },        
        closeBtn: function() {
            this.showWindow = false;
        }        
    }
});

var errorWindow = new Vue({
    el: "#errorWindow",
    data: {
        showWindow: false,
        text: ""
    },
    methods:{
        Show:function(message) {
            this.text = message;
            this.showWindow = true;
        },
        CloseWindow: function() {
            this.showWindow = false;
        }        
    }
});


//Функция для работы с апи. Возвращает Promise 
function apiRequest(url,method='POST',jsonData=null){
     console.log(url)
    let param;
    if (jsonData == null){
        param ={
            method: method,
            cache: 'no-cache',
            credentials: "same-origin"
        }
    }else{
        postData = new FormData()
        postData.append('json', JSON.stringify(jsonData))          
        param ={
            method: method,
            cache: 'no-cache',
            credentials: "same-origin",
            body: postData
        }
    }
    return fetch(url,param)
    .then(status)
    .then(json)
}

function status(response) {  
    if (response.status >= 200 && response.status < 300) {  
        return Promise.resolve(response)  
    } else {  
        return Promise.reject(new Error(response.statusText))  
    }  
}
function json(response) {
    console.log("response")
    console.log(response)
    return response.json()  
}

function addDays(date, days) {
  var result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

