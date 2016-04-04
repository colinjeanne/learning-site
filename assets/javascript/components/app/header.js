import Constants from '../../constants/constants';
import React from 'react';
import TabbedNavigation from './tabbedNavigation';
import UserButton from './userButton';

const header = props => {
    const isSignedIn = !!props.displayName;
    const navigationElement = (
        <TabbedNavigation
            id="mainNavigation"
            onSelect={props.onTabSelected}
            selectedPage={props.selectedPage}
            tabs={Constants.navigationPages} />
    );
    const navigation = isSignedIn ? navigationElement : undefined;
    
    return (
        <header>
            <h1>Isaac's Learning Site</h1>
            <span>
                {navigation}
                <UserButton
                    displayName={props.displayName}
                    onClick={props.onUserMenuClick}
                    toggled={props.selectedPage === Constants.pages.PROFILE} />
            </span>
        </header>
    )};

header.propTypes = {
    displayName: React.PropTypes.string,
    onTabSelected: React.PropTypes.func.isRequired,
    onUserMenuClick: React.PropTypes.func.isRequired,
    selectedPage: React.PropTypes.string
};

export default header;