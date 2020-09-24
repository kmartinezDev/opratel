var mongoose = require('mongoose');
var Schema = mongoose.Schema;

mongoose.connect("mongodb://localhost/user_db")

var user_schema = new Schema({
    username: { type:String, required:true },
    password: { type:String, required:true },
    email: {type:String, required:true },
    active: {type:Boolean }
})

var User = mongoose.model('User', user_schema)

module.exports = User