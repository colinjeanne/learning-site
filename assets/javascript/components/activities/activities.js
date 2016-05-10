import { activitiesPropType } from './../propTypes';
import ActivityFilter from './activityFilter';
import ActivityList from './activityList';
import React from 'react';

const activities = props => {
    const sorted = [...props.activities].sort((a, b) => {
        if (a.title < b.title) {
            return -1;
        } else if (a.title > b.title) {
            return 1;
        }
        return 0;
    });
    
    return (
        <section>
            <h1 className="activitiesHeader">
                <button
                    onClick={() => props.onAddActivity()}
                    type="button">
                    Add Activity
                </button>
                <ActivityFilter
                    onChange={props.onFilterActivities} />
            </h1>
            <ActivityList
                activities={props.activities}
                onSelectActivity={props.onSelectActivity} />
        </section>
    )};

activities.propTypes = {
    activities: activitiesPropType.isRequired,
    onAddActivity: React.PropTypes.func.isRequired,
    onFilterActivities: React.PropTypes.func.isRequired,
    onSelectActivity: React.PropTypes.func.isRequired
};

export default activities;