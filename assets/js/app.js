import React from 'react';
import ReactDOM, {render} from 'react-dom';
import PickLocation from './picklocation';
import EditLocation from './editlocation';
import ProductMap from './productMap';
import 'bootstrap-datetime-picker';
import 'bootstrap-less';
import {Provider} from 'react-redux';
import {createStore} from 'redux';
import Card from './components/Card';
import rootReducer from './reducers';

const $ = require('jquery');
require('bootstrap');
require('../css/app.scss');

const mapStore = createStore(rootReducer);

if (document.getElementById('product-map') !== null){
    ReactDOM.render(
        <Provider store={mapStore} >
            <ProductMap />
            {/* <Card /> */}
        </Provider>,
        document.getElementById('product-map')
    );
}

if (document.getElementById('product-list') !== null){
    ReactDOM.render(
        <Provider store={mapStore} >
            <Card />
        </Provider>,
        document.getElementById('product-list')
    );
}

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
