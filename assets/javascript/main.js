import AppContainer from './components/app/container';
import { Provider } from 'react-redux';
import React from 'react';
import ReactDOM from 'react-dom';
import { signInUser } from './actions/user';
import store from './stores/store';

ReactDOM.render(
    <Provider store={store}>
        <AppContainer />
    </Provider>,
    document.getElementById('app')
);

window.signinSucceeded = user => {
    store.dispatch(signInUser(user));
};

window.signinFailed = error => store.dispatch(signInUser(error));