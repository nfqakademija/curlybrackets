import React, {useCallback, useState, useRef, Component} from 'react';
import { GoogleMap, withGoogleMap, withScriptjs, Marker } from 'react-google-maps';


class EditLocation extends Component {
    render(){
        return(
            <div>labas</div>
        );
    }
}

export default EditLocation;
// function EditMap() {
//     //get values from form inputs
//     let oldLat = document.getElementById('location_latitude').value;
//     let oldLng = document.getElementById('location_longitude').value;
//     oldLat = Number(oldLat);
//     oldLng = Number(oldLng);

//     console.log(oldLat);

//     const [center, setCenter] = useState({ lat: 54.68916, lng: 25.2798 });
//     const refMap = useRef(null);
  
//     const handleBoundsChanged = () => {
//       const mapCenter = refMap.current.getCenter();
//       setCenter(mapCenter);
//       //get values of marker
//       let lat = refMap.current.getCenter().lat();
//       let lng = refMap.current.getCenter().lng();
//       //insert values to forms
//       document.getElementById('location_latitude').value = lat;
//       document.getElementById('location_longitude').value = lng;
//       return
//     };
  
//     return (
//       <GoogleMap
//         ref={refMap}
//         defaultZoom={13}
//         defaultCenter={{ lat: 54.68916, lng: 25.2798 }}
//         onBoundsChanged={useCallback(handleBoundsChanged)}
//       >
//         <Marker position={center} />
//       </GoogleMap>
//     );
//   }

//     const WrappedMap = withScriptjs(withGoogleMap(EditMap));

// export default function EditLocation(){
//     return(
//         <div>
//             <WrappedMap 
//                 googleMapURL={'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry,drawing,places&key=AIzaSyB3_YPiJ8Pa2l8tFzKQ_hqK57qjnu5-KmM'}
//                 loadingElement={<div style={{ height: `100%` }} />}
//                 containerElement={<div style={{ height: `400px` }} />}
//                 mapElement={<div style={{ height: `100%` }} />}
//             />
//         </div>
//     )
// }