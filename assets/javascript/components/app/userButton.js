import Constants from './../../constants/constants';
import React from 'react';

const userButton = props => {
    const className = props.isSignedIn ? 'g-signin2 signedIn' : 'g-signin2';
    const narrowClassName = props.isSignedIn ?
        'userTileNarrow signedIn' :
        'userTileNarrow';
    const toggledClass = props.toggled ? 'userTileToggled' : undefined;

    const PROFILE = Constants.pages.PROFILE;

    const buttonElement = (
        <span
            className={toggledClass}>
            <button
                className="userContainerNarrow"
                onClick={() => props.onClick(PROFILE)}>
                {'\uD83D\uDEBA'}
            </button>
            <button
                className="userContainer"
                onClick={() => props.onClick(PROFILE)}>
                {props.displayName}
            </button>
        </span>
    );

    return (
        <span id="userTile">
            {props.isSignedIn ? buttonElement : undefined}
            <div className={narrowClassName}>
                <a href="/oauth">Sign In</a>
            </div>
            <div
                className={className}
                data-onfailure="signinFailed"
                data-onsuccess="signinSucceeded"></div>
        </span>
    )};

userButton.propTypes = {
    displayName: React.PropTypes.string,
    isSignedIn: React.PropTypes.bool,
    onClick: React.PropTypes.func.isRequired,
    toggled: React.PropTypes.bool.isRequired
};

export default userButton;