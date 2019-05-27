import React, {Component} from 'react';
import fetchData from './ajax';
import GoogleMapReact from 'google-map-react';

const getMapBounds = (map, maps, places) => {
    const bounds = new maps.LatLngBounds();
  
    places.forEach((place) => {
      bounds.extend(new maps.LatLng(
        place.geometry.location.lat,
        place.geometry.location.lng,
      ));
    });
    return bounds;
  };

  const bindResizeListener = (map, maps, bounds) => {
    maps.event.addDomListenerOnce(map, 'idle', () => {
      maps.event.addDomListener(window, 'resize', () => {
        map.fitBounds(bounds);
      });
    });
  };

  const apiIsLoaded = (map, maps, places) => {
    const bounds = getMapBounds(map, maps, places);
    map.fitBounds(bounds);
    bindResizeListener(map, maps, bounds);
  };
  
const CustomMarker = () => <div><img src="https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi2.png" /></div>;


class ProductMap extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            places: []
        };
    }

    componentDidMount() {
        fetch('/product/jsonIndex')
            .then(response => response.json())
            .then(data => this.setState({places: data}));
    }

    render() {
        const {places} = this.state;
        return (
            <div style={{height: '400px', width: '100%' }}>
                <GoogleMapReact
                bootstrapURLKeys={{ key: 'AIzaSyB3_YPiJ8Pa2l8tFzKQ_hqK57qjnu5-KmM' }}
                defaultCenter={{ lat: 54.68916, lng: 25.2798 }}
                defaultZoom={13}
                >
                    {places.map(place => (
                        <CustomMarker
                        key={place.product_id}
                        text={place.title}
                        lat={place.latitude}
                        lng={place.longitude}
                        />
                    ))}
                    
                </GoogleMapReact>
            </div>
        );
    }
};

export default ProductMap;













// function ProductMap() {
    
//     const refMap = useRef(null);
    
//     //getting viewport locations of map
//     const mapBounds = () => {
//         const bounds = refMap.current.getBounds();
//         let NE = bounds.getNorthEast();
//         let SW = bounds.getSouthWest();
//         let NELat = NE.lat();
//         let NELng = NE.lng();
//         let SWLat = SW.lat();
//         let SWLng = SW.lng();

//         fetchData(NELat, NELng, SWLat, SWLng);

//         console.log(`Siaures rytai ${NE}`);
//         console.log(`Siaures rytai latitude ${NELat}`);
//         console.log(`Siaures rytai longitude ${NELng}`);
//         console.log(`Pietvakariai ${SW}`);
//         console.log(`Pietvakariai latitude ${SWLat}`);
//         console.log(`Pietvakariai longitude ${SWLng}`);
//         return;
//     }

//     let loaded = false;
//     const initialLoad = () => {
//         if(!loaded){
//             console.log('uzkrove');
//             loaded = true;
//             return mapBounds();
//         }
//     }
  
//     return (
//       <GoogleMap
//         ref={refMap}
//         defaultZoom={13}
//         defaultCenter={{ lat: 54.68916, lng: 25.2798 }}
//         onBoundsChanged={useCallback(initialLoad)}
//         onDragEnd={useCallback(mapBounds)}
//       >
//          {fetchData()}
//       </GoogleMap>
//     );
//   }

//     const WrappedMap = withScriptjs(withGoogleMap(ProductMap));

// export default function ProductLocation(){
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
