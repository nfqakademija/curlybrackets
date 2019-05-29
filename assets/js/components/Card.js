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
    console.log(place);
    return (
        <div >
            <h2>jhbsdjfbhadj</h2>
        </div>
    );
};

function PostList({ places }) {
    console.log(places);
    return (
        <div>
            {places.map(place => {
                console.log(place);
                return (
                    <Item place={place} key={place.product_id}/>
                );
            })}
        </div>
    );
}

const mapStateToProps = state => {
    console.log(state);
    return {
        ...state
    };
};


export default connect(
    mapStateToProps,
    null
)(PostList);