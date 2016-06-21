import TabbedNavigationButton from './tabbedNavigationButton';
import React from 'react';

const tabbedNavigation = props => {
    const children = Object.keys(props.tabs).map(
        tab => (
            <TabbedNavigationButton
                id={tab}
                key={tab}
                onSelect={props.onSelect}
                selected={props.selectedPage === tab}
                title={props.tabs[tab]} />
        )
    );

    return (
        <nav id="mainNavigation">
            <ol className="tabbedNavigation">
                {children}
            </ol>
        </nav>
    );
};

tabbedNavigation.propTypes = {
    onSelect: React.PropTypes.func.isRequired,
    selectedPage: React.PropTypes.string,
    tabs: React.PropTypes.object.isRequired
};

export default tabbedNavigation;