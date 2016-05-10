import { applyMiddleware, createStore } from 'redux';
import multi from 'redux-multi';
import reducer from '../reducers/reducer';
import thunkMiddleware from 'redux-thunk';

const createStoreWithMiddleware = applyMiddleware(
    multi,
    thunkMiddleware
)(createStore);

export default createStoreWithMiddleware(reducer);