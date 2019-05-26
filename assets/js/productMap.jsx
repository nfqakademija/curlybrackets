import React, {useCallback, useState, useRef} from 'react';
import { GoogleMap, withGoogleMap, withScriptjs, Marker } from 'react-google-maps';

function ProductMap() {
    const refMap = useRef(null);
    
    //getting viewport locations of map
    const mapBounds = () => {
        const bounds = refMap.current.getBounds();
        let NE = bounds.getNorthEast();
        let SW = bounds.getSouthWest();
        let NELat = NE.lat();
        let NELng = NE.lng();
        let SWLat = SW.lat();
        let SWLng = SW.lng();



        console.log(`Siaures rytai ${NE}`);
        console.log(`Siaures rytai latitude ${NELat}`);
        console.log(`Siaures rytai longitude ${NELng}`);
        console.log(`Pietvakariai ${SW}`);
        console.log(`Pietvakariai latitude ${SWLat}`);
        console.log(`Pietvakariai longitude ${SWLng}`);
        return;
    }

    let loaded = false;
    const initialLoad = () => {
        if(!loaded){
            console.log('uzkrove');
            loaded = true;
            return mapBounds();
        }
    }
  
    return (
      <GoogleMap
        ref={refMap}
        defaultZoom={13}
        defaultCenter={{ lat: 54.68916, lng: 25.2798 }}
        onBoundsChanged={useCallback(initialLoad)}
        onDragEnd={useCallback(mapBounds)}
      >
        <Marker />
      </GoogleMap>
    );
  }

    const WrappedMap = withScriptjs(withGoogleMap(ProductMap));

export default function ProductLocation(){
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
