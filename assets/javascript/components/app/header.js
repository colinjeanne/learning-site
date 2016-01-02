import React from 'react';

const header = props => {
    const className = props.displayName ? 'g-signin2 signedIn' : 'g-signin2';
    return (
        <header>
            Isaac's Learning Site
            <span id="userContainer">{props.displayName}</span>
            <div
                className={className}
                data-onfailure="signinFailed"
                data-onsuccess="signinSucceeded"></div>
        </header>
    )};

header.propTypes = {
    displayName: React.PropTypes.string
};

export default header;