import Constants from '../../constants/constants';
import Header from './header.js';
import React from 'react';
import TabbedNavigation from './tabbedNavigation';

const app = props => {
    let content;
    if (props.displayName) {
        let page;
        switch (props.page) {
            case Constants.pages.ACTIVITIES:
                break;
            
            case Constants.pages.CHILD_PROGRESS:
                break;
            
            case Constants.pages.ADD_ACTIVITY:
                break;
            
            case Constants.pages.PROFILE:
                break;
        }
        
        content = (
            <div>
                <Header displayName={props.displayName} />
                <TabbedNavigation
                    id="mainNavigation"
                    onSelect={props.onTabSelected}
                    selectedPage={props.page}
                    tabs={Constants.pages} />
                {page}
            </div>
        );
    } else {
        content = (
            <div>
                <Header displayName={props.displayName} />
            </div>
        );
    }
    
    return content;
};

app.propTypes = {
    displayName: React.PropTypes.string,
    onTabSelected: React.PropTypes.func.isRequired,
    page: React.PropTypes.string.isRequired,
};

export default app;