import TabbedNavigationButton from './tabbedNavigationButton';
import React from 'react';

const tabbedNavigation = props => {
    const children = Object.keys(props.tabs).map(
        tab => {
            return (
                <TabbedNavigationButton
                    id={props.tabs[tab]}
                    key={tab}
                    onSelect={props.onSelect}
                    selected={props.selectedPage === props.tabs[tab]}
                    title={props.tabs[tab]} />
            );
        }
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
    selectedPage: React.PropTypes.string.isRequired,
    tabs: React.PropTypes.objectOf(React.PropTypes.string).isRequired
};

export default tabbedNavigation;