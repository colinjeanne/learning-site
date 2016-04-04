import Constants from './../../constants/constants';
import React from 'react';

const userButton = props => {
    const isSignedIn = !!props.displayName;
    const className = isSignedIn ? 'g-signin2 signedIn' : 'g-signin2';
    const toggledClass = props.toggled ? 'userTileToggled' : undefined;
    
    const PROFILE = Constants.pages.PROFILE;
    
    const buttonElement = (
        <span
            className={toggledClass}>
            <button
                id="userContainer"
                onClick={() => props.onClick(PROFILE)}>
                {props.displayName}
            </button>
        </span>
    );
    
    return (
        <span id="userTile">
            {isSignedIn ? buttonElement : undefined}
            <div
                className={className}
                data-onfailure="signinFailed"
                data-onsuccess="signinSucceeded"></div>
        </span>
    )};

userButton.propTypes = {
    displayName: React.PropTypes.string,
    onClick: React.PropTypes.func.isRequired,
    toggled: React.PropTypes.bool.isRequired
};

export default userButton;