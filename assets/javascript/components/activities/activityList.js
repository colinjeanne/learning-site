import { activitiesPropType } from './../propTypes';
import ActivityItem from './activityItem';
import React from 'react';

const activityList = props => {
    const sorted = [...props.activities].sort((a, b) => {
        if (a.name < b.name) {
            return -1;
        } else if (a.name > b.name) {
            return 1;
        }
        return 0;
    });
    
    const items = sorted.map(activity => (
        <li
            key={activity.links.self}
            onClick={() => props.onSelectActivity(activity)}>
            <ActivityItem
                activity={activity} />
        </li>
    ));
    
    const listNode = (
        <ol>
            {items}
        </ol>
    );
    
    const emptyMessageNode = (
        <div>You don't have any activities.</div>
    );
    
    const contentNode = items.length ? listNode : emptyMessageNode;
    
    return (
        <section className="activitiesList">
            {contentNode}
        </section>
    );
};

activityList.propTypes = {
    activities: activitiesPropType.isRequired,
    onSelectActivity: React.PropTypes.func.isRequired
};

export default activityList;