import React from 'react';
import ReactDOM from 'react-dom';
import PickLocation from './picklocation';
import EditLocation from './editlocation';

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

require('../css/app.scss');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

//add location
if (document.getElementById('map') !== null){
    ReactDOM.render(<PickLocation />, document.getElementById('map'));   
}

//edit location
if(document.getElementById('mapEdit') !== null){
    ReactDOM.render(<EditLocation />, document.getElementById('mapEdit'));
}