import React, {useCallback, useState, useRef} from 'react';
import { GoogleMap, withGoogleMap, withScriptjs, Marker } from 'react-google-maps';

function ProductMap() {
    const [center, setCenter] = useState({ lat: 54.68916, lng: 25.2798 });
    const refMap = useRef(null);
 
    const mapBounds = () => {
        const bounds = refMap.current.getBounds();
        let NE = bounds.getNorthEast();
        let SW = bounds.getSouthWest();

        console.log(`Siaures rytai ${NE}`);
        console.log(`Pietvakariai ${SW}`);

        return;
    }
  
    return (
      <GoogleMap
        ref={refMap}
        defaultZoom={13}
        defaultCenter={{ lat: 54.68916, lng: 25.2798 }}
        onBoundsChanged={useCallback(mapBounds)}
      >
        <Marker position={center} />
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
