var User = require('../models/user')
var fs = require('fs')

module.exports = (req, res, next) =>{
    User.findOne({'username': req.params.id}, (err, data) => {
        if(data != null){
            res.locals.userData = data
            next()
        }
        else{
            fs.writeFile('./log.txt', `[${Date().toString()}] ERROR - Hubo un error al intentar procesar request ${req.url}  \n`, {flag: 'a+'}, (err) => {
                if (err) { throw err; }
            });
            res.end()
        }
    })
}