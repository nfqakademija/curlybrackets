import React from 'react';
import { GoogleMap, withScriptjs, withGoogleMap, Marker} from 'react-google-maps';
// import LocationPicker from 'react-location-picker';

function Map() {
    return(
        <GoogleMap
                defaultZoom={13}
                defaultCenter={{lat:54.68916, lng:25.2798}}
        >
           <Marker 
                position={{lat:54.68916, lng:25.2798}}
                draggable={true}
                onDragEnd={(e) => markerDrop(e)}
           />
        </GoogleMap>
    );
}

function markerDrop(event){
    //get values of marker
    let lat = event.latLng.lat();
    let lng = event.latLng.lng();
    //insert values to forms
    document.getElementById('location_latitude').value = lat;
    document.getElementById('location_longitude').value = lng;
}

const WrappedMap = withScriptjs(withGoogleMap(Map));



export default function PickLocation(){
    return(
        <div>
            <WrappedMap 
                googleMapURL={'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry,drawing,places&key=AIzaSyB3_YPiJ8Pa2l8tFzKQ_hqK57qjnu5-KmM'}
                loadingElement={<div style={{ height: `100%` }} />}
                containerElement={<div style={{ height: `400px` }} />}
                mapElement={<div style={{ height: `100%` }} />}
            />
        </div>
    )
}