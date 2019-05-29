import React from 'react';
import ReactDOM from 'react-dom';
import PickLocation from './picklocation';
import EditLocation from './editlocation';
import ProductMap from './productMap';
import 'bootstrap-datetime-picker';
import 'bootstrap-less';

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

require('../css/app.scss');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('.datetimepicker').datetimepicker({format: 'yyyy-mm-dd hh:ii'});

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

//maps of products
if(document.getElementById('product-map') !== null){
    ReactDOM.render(<ProductMap />, document.getElementById('product-map'));
}