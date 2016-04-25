import Constants from '../../constants/constants';
import Header from './header.js';
import Progress from './../progress/container';
import Profile from './../profile/container';
import React from 'react';
import Unauthenticated from './../unauthenticated/unauthenticated';

const pageToContent = {
    [Constants.pages.ACTIVITIES]: () => {},
    
    [Constants.pages.ADD_ACTIVITY]: () => {},
    
    [Constants.pages.CHILD_PROGRESS]: () => (<Progress />),
    
    [Constants.pages.PROFILE]: props => (<Profile />),
    
    [Constants.pages.UNAUTHENTICATED]: () => (<Unauthenticated />)
};

const app = props => {
    const page = pageToContent[props.page](props);
    return (
        <div id="root">
            <Header
                displayName={props.displayName}
                onTabSelected={props.onTabSelected}
                onUserMenuClick={props.onUserMenuClick}
                selectedPage={props.page} />
            {page}
        </div>
    );
};

app.propTypes = {
    displayName: React.PropTypes.string,
    onTabSelected: React.PropTypes.func.isRequired,
    onUserMenuClick: React.PropTypes.func.isRequired,
    page: React.PropTypes.string,
};

export default app;