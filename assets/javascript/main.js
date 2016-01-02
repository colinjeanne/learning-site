import AppContainer from './components/app/container';
import { Provider } from 'react-redux';
import React from 'react';
import ReactDOM from 'react-dom';
import store from './stores/store';

ReactDOM.render(
    <Provider store={store}>
        <AppContainer />
    </Provider>,
    document.getElementById('app')
);

window.signinSucceeded = user => {
    console.log(user);
};

window.signinFailed = error => console.log(error);