import AppContainer from './components/app/container';
import { getMe, signInUser, updateMe } from './actions/user';
import { getMyActivities } from './actions/activity';
import { getMyFamily } from './actions/family';
import { getMyInvitations } from './actions/invite';
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
    store.dispatch(signInUser(user));
    store.dispatch(getMyInvitations());
    store.dispatch(getMe()).
        then(action =>
            store.dispatch(
                updateMe(
                    action.payload.links.self,
                    user.getBasicProfile().getName())));
    store.dispatch(getMyFamily());
    store.dispatch(getMyActivities());
};

window.signinFailed = error => store.dispatch(signInUser(error));