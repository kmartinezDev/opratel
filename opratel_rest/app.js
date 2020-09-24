var express = require('express')
var User = require('./models/user')
var bodyParser = require("body-parser")
var fs = require('fs')

var app = express()

app.use(bodyParser.json())
app.use(bodyParser.urlencoded({extended: true}))

var get_user_middleware = require('./middlewares/get_user')

app.all('/user/get_user/:id', get_user_middleware)
app.all('/user/activate_user/:id', get_user_middleware)
app.all('/user/deactivate_user/:id', get_user_middleware)

app.post('/user/add_user', (req, res)=>{
    
    let data = {
        username: req.body.username,
        password: req.body.password,
        email: req.body.email,
        active: 0
    }

    let user = new User(data)

    user.save((err)=>{
        if(!err){
            writeLog(`INFO - Procesamos request add_user | username: ${req.body.username} | password: ${req.body.password} | email: ${req.body.email}`)
            res.json({status_code: 0});
        }
        else{
            writeLog(`ERROR - "Hubo un error al intentar procesar request add_user"`)
            res.end()
        }
    })
})

app.get('/user/get_user/:id', (req, res)=>{
    
    response = {
        username: res.locals.userData.username,
        password: res.locals.userData.password,
        email: res.locals.userData.email,
    }

    writeLog(`INFO - Procesamos request get_user | username: ${req.params.id}`)

    res.json(response);
})

app.post('/user/activate_user/:id', (req, res)=>{
    
    res.locals.userData.active = 1
    res.locals.userData.save((err)=>{
        if(!err){
            writeLog(`INFO - Procesamos request activate_user | username: ${req.body.username}`)
            res.json({status_code: 0});
        }
        else{
            writeLog(`ERROR - "Hubo un error al intentar procesar request activate_user"`)
            res.end()
        }
    })
})


app.post('/user/deactivate_user/:id', (req, res)=>{
    
    res.locals.userData.active = 0
    res.locals.userData.save((err)=>{
        if(!err){
            writeLog(`INFO - Procesamos request deactivate_user | username: ${req.body.username}`)
            res.json({status_code: 0});
        }
        else{
            writeLog(`ERROR - "Hubo un error al intentar procesar request deactivate_user"`)
            res.end()
        }
    })
})

function writeLog(data){
    fs.writeFile('./log.txt', `[${Date().toString()}] ${data}  \n`, {flag: 'a+'}, (err) => {
        if (err) {
            throw err;
        }
    });
}

app.listen(8080)