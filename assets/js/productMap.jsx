import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import GoogleMap from './components/GoogleMap';
import {update} from './actions'
import {connect} from 'react-redux';

const InfoWindow = (props) => {
    const { place } = props;
    const infoWindowStyle = {
        position: 'relative',
        left: '10px',
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
              {place.description}
            </div>
            <div className="info-window-deadline">
                Baigia galioti {place.deadline}
            </div>
            <div className="info-window-profile-btn">
                <a target="_blank" className="call-to-btn-green" href={place.contact_url}>
                  Susisiekti <i className="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    );
};

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
            {(props.lock || props.show) && <InfoWindow place={props.place} />}
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
    componentDidMount(props) {
        fetch('/product/jsonIndex')
            .then(response => response.json())
            .then((data) => {
                data.forEach((result) => {
                    result.show = false;
                });
                this.props.onAddPost(this.state.places);
            });
    }
    updateMarkers = (props, dispatch) => {
      const latitudeSE = props.bounds.se.lat;
      const longitudeSE = props.bounds.se.lng;
      const latitudeNW = props.bounds.nw.lat;
      const longitudeNW = props.bounds.nw.lng;

      const url = '/product/jsonMap?latitudeSE=' + latitudeSE + '&longitudeSE=' + longitudeSE + '&latitudeNW=' + latitudeNW + '&longitudeNW=' + longitudeNW;
        fetch(url)
            .then(response => response.json())
            .then((data) => {
                data.forEach((result) => {
                    result.show = false;
                    result.lock = false;
                });
                this.setState({ places: data });
                this.props.onAddPost(this.state.places);
            });
    };

    onChildClickCallback = (key) => {
        this.setState((state) => {
            const index = state.places.findIndex(e => e.product_id == key);
            state.places[index].lock = !state.places[index].lock;
            return { places: state.places };
        });
    };
    _onChildMouseEnter = (key, childProps) => {
        this.setState((state) => {
            const index = state.places.findIndex(e => e.product_id == key);
            state.places[index].show = true;
            return { places: state.places };
        });
    }
    _onChildMouseLeave = (key, childProps) => {
        this.setState((state) => {
            const index = state.places.findIndex(e => e.product_id == key);
            state.places[index].show = false;
            return { places: state.places };
        });
    }
    render() {
        const { places } = this.state;
        return (
            <Fragment>
                {(
                    <GoogleMap
                        defaultZoom={13}
                        defaultCenter={{lat: 54.687839, lng: 25.28784}}
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

const mapDispatchToProps = dispatch => {
  return {
      onAddPost: places => {
          dispatch(update(places));
      }
  };
};

export default connect(
  null,
  mapDispatchToProps
)(MarkerInfoWindow);