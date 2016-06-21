import Activities from './../activities/container';
import Constants from './../../constants/constants';
import EditActivity from './../editActivity/container';
import Header from './header.js';
import Progress from './../progress/container';
import Profile from './../profile/container';
import React from 'react';
import Unauthenticated from './../unauthenticated/unauthenticated';
import ViewActivity from './../viewActivity/container';

const pageToContent = {
    [Constants.pages.ACTIVITIES]: () => (<Activities />),

    [Constants.pages.ADD_ACTIVITY]: () => (<EditActivity />),

    [Constants.pages.CHILD_PROGRESS]: () => (<Progress />),

    [Constants.pages.EDIT_ACTIVITY]: () => (<EditActivity />),

    [Constants.pages.PROFILE]: props => (<Profile />),

    [Constants.pages.UNAUTHENTICATED]: () => (<Unauthenticated />),

    [Constants.pages.VIEW_ACTIVITY]: () => (<ViewActivity />)
};

const app = props => {
    const page = pageToContent[props.page](props);
    return (
        <div id="root">
            <Header
                displayName={props.displayName}
                isSignedIn={props.isSignedIn}
                onTabSelected={props.onTabSelected}
                onUserMenuClick={props.onUserMenuClick}
                selectedPage={props.page} />
            {page}
        </div>
    );
};

app.propTypes = {
    displayName: React.PropTypes.string,
    isSignedIn: React.PropTypes.bool,
    onTabSelected: React.PropTypes.func.isRequired,
    onUserMenuClick: React.PropTypes.func.isRequired,
    page: React.PropTypes.string,
};

export default app;