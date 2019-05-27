import React from 'react';
import Marker from 'react-google-maps';

const fetchData = (NELat, NELng, SWLat, SWLng) => {
    // let url = '/product/jsonMap';
    let url = '/product/jsonIndex';

    fetch( url, {method: 'GET'} )
    .then( response => response.json() )
    .then( data => {
            const markers = data.map( () =>
                <Marker position={{ lat: `${data[0].location.latitude}`, lng: `${data[0].location.longitude}` }}></Marker>
            );
            console.log(data);
            console.log(markers);
            return markers;
    })
    .catch( error => console.error(`Error: ${error}`));
}

export default fetchData;