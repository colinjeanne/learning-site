import { activityPropType } from './../propTypes';
import React from 'react';

const activityItem = props => {
    return (
        <section className="activityItem">
            {props.activity.name}
        </section>
    )};

activityItem.propTypes = {
    activity: activityPropType.isRequired
};

export default activityItem;