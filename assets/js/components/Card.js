import React from 'react';
import { connect } from 'react-redux';

const styles = {
    borderBottom: '2px solid #eee',
    background: '#fafafa',
    margin: '.75rem auto',
    padding: '.6rem 1rem',
    maxWidth: '500px',
    borderRadius: '7px'
};

const Item = ({ place }) => {
    return (
        <div className='col-md-3 product-box-margin'>
            <div className='product-box'>
                <div>
                    <img className='product-list-img' src={place.image} alt='Produkto nuotrauka' />
                </div>
                <div className='product-user'>
                    <img className='product-avatar' src="#" alt="Avataras" />
                    <span className='product-avatar-user'>User name</span>
                </div>
                <div className='product-list-title'>
                    <h4>{place.title}</h4>
                </div>
                <div className="product-list-description">
                    {place.description}
                </div>
                <div className="product-list-deadline">
                    Baigia galioti {place.deadline}
                </div>
                <div className="profile-btn-block">
                    <a target="_blank" className="call-to-btn-green" href={place.contact_url}>
                        Susisiekti <i className="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
    );
};

function PostList({ places }) {
    return (
        <div className='row'>
            {places.map(place => {
                return (
                    <Item place={place} key={place.product_id}/>
                );
            })}
        </div>
    );
}

const mapStateToProps = state => {
    return {
        ...state
    };
};


export default connect(
    mapStateToProps,
    null
)(PostList);