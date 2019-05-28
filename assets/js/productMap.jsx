import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import isEmpty from 'lodash.isempty';
// examples:
import GoogleMap from './components/GoogleMap';
// consts: [34.0522, -118.2437]
const LOS_ANGELES_CENTER = [54.687839, 25.28784];
// InfoWindow component
const InfoWindow = (props) => {
    const { place } = props;
    const infoWindowStyle = {
        position: 'relative',
        bottom: 150,
        left: '-45px',
        width: 220,
        backgroundColor: 'white',
        boxShadow: '0 2px 7px 1px rgba(0, 0, 0, 0.3)',
        padding: 10,
        fontSize: 14,
        zIndex: 100,
    };
    return (
        <div style={infoWindowStyle}>
            <div className="info-window-title">
                {place.title}
            </div>
            <div>
              <img src={place.image} />
            </div>
            <div className="info-window-profile-btn">
                <a target="_blank" className="call-to-btn-green" href="{{ path('contact', {'id': product.id}) }}">
                  Susisiekti <i className="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    );
};
// Marker component
const Marker = (props) => {
    const markerStyle = {
        border: '1px solid white',
        borderRadius: '50%',
        height: 10,
        width: 10,
        backgroundColor: props.show ? 'red' : 'blue',
        cursor: 'pointer',
        zIndex: 10,
    };
    return (
        <Fragment>
            <div style={markerStyle} />
            {props.show && <InfoWindow place={props.place} />}
            {props.lock && <InfoWindow place={props.place} />}
        </Fragment>
    );
};
class MarkerInfoWindow extends Component {
    constructor(props) {
        super(props);
        this.state = {
            places: [],
        };
    }
    componentDidMount() {
        console.log('fetching init');
        fetch('/product/jsonIndex')
            .then(response => response.json())
            .then((data) => {
                data.forEach((result) => {
                    result.show = false;
                });
                this.setState({ places: data });
            });
    }
    updateMarkers = (props, dispatch) => {
        console.log('fetching update');
        console.log(props);
        // dispatch(update(input.value));
        fetch('/product/jsonIndex')
            .then(response => response.json())
            .then((data) => {
                data.forEach((result) => {
                    result.show = false; // eslint-disable-line no-param-reassign
                    result.lock = false;
                });
                this.setState({ places: data });
            });
    };
    // onChildClick callback can take two arguments: key and childProps
    onChildClickCallback = (key) => {
        console.log('clicked child');
        this.setState((state) => {
            const index = state.places.findIndex(e => e.product_id == key);
            state.places[index].lock = !state.places[index].lock; // eslint-disable-line no-param-reassign
            return { places: state.places };
        });
    };
    _onChildMouseEnter = (key, childProps) => {
        console.log('hover effect');
        console.log(key);
        console.log(childProps);
        this.setState((state) => {
            const index = state.places.findIndex(e => e.product_id == key);
            state.places[index].show = !state.places[index].show; // eslint-disable-line no-param-reassign
            return { places: state.places };
        });
    }
    _onChildMouseLeave = (key, childProps) => {
        console.log('hover leave');
        console.log(key);
        console.log(childProps);
        this.setState((state) => {
            const index = state.places.findIndex(e => e.product_id == key);
            state.places[index].show = !state.places[index].show; // eslint-disable-line no-param-reassign
            return { places: state.places };
        });
    }
    render() {
        const { places } = this.state;
        return (
            <Fragment>
                {!isEmpty(places) && (
                    <GoogleMap
                        defaultZoom={13}
                        defaultCenter={LOS_ANGELES_CENTER}
                        bootstrapURLKeys={{ key: 'AIzaSyB3_YPiJ8Pa2l8tFzKQ_hqK57qjnu5-KmM' }}
                        yesIWantToUseGoogleMapApiInternals
                        onChildClick={this.onChildClickCallback}
                        onChildMouseEnter={this._onChildMouseEnter}
                        onChildMouseLeave={this._onChildMouseLeave}
                        onChange={this.updateMarkers}
                    >
                        {places.map(place =>
                            (<Marker
                                key={place.product_id}
                                lat={place.latitude}
                                lng={place.longitude}
                                show={place.show}
                                lock={place.lock}
                                place={place}
                            />))}
                    </GoogleMap>
                )}
            </Fragment>
        );
    }
}
InfoWindow.propTypes = {
    place: PropTypes.shape({
        name: PropTypes.string,
        formatted_address: PropTypes.string,
        rating: PropTypes.number,
        types: PropTypes.array,
        price_level: PropTypes.number,
        opening_hours: PropTypes.object,
    }).isRequired,
};
Marker.propTypes = {
    show: PropTypes.bool.isRequired,
    place: PropTypes.shape({
        name: PropTypes.string,
        formatted_address: PropTypes.string,
        rating: PropTypes.number,
        types: PropTypes.array,
        price_level: PropTypes.number,
        opening_hours: PropTypes.object,
    }).isRequired,
};

export default MarkerInfoWindow;