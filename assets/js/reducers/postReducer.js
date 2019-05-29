export default function postReducer(state = [], action) {
    switch (action.type) {
        case 'UPDATE':
            return action.places;
        default:
            return state;
    }
}