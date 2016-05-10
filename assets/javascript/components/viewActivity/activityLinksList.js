import ActivityLink from './activityLink';
import { activityLinksPropType } from './../propTypes';
import React from 'react';

const activityLinksList = props => {
    const sorted = [...props.activityLinks].sort((a, b) => {
        if (a.title < b.title) {
            return -1;
        } else if (a.title > b.title) {
            return 1;
        }
        return 0;
    });
    
    const items = sorted.map(activityLink => (
        <li key={activityLink.uri}>
            <ActivityLink
                activityLink={activityLink} />
        </li>
    ));
    
    return (
        <section className="activityLinksList">
            <ul>
                {items}
            </ul>
        </section>
    );
};

activityLinksList.propTypes = {
    activityLinks: activityLinksPropType
};

export default activityLinksList;