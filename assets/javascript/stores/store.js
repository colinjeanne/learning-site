import { applyMiddleware, createStore } from 'redux';
import reducer from '../reducers/reducer';
import thunkMiddleware from 'redux-thunk';

const createStoreWithMiddleware = applyMiddleware(
    thunkMiddleware
)(createStore);

export default createStoreWithMiddleware(reducer);