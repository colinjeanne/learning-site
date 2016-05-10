import React from 'react';

export const activityLinkPropType = React.PropTypes.shape({
    title: React.PropTypes.string.isRequired,
    uri: React.PropTypes.string.isRequired
});

export const activityLinksPropType =
    React.PropTypes.arrayOf(activityLinkPropType);

export const activityPropType = React.PropTypes.shape({
    activityLinks: activityLinksPropType,
    description: React.PropTypes.string,
    links: React.PropTypes.shape({
        self: React.PropTypes.string.isRequired
    }),
    name: React.PropTypes.string.isRequired
});

export const activitiesPropType = React.PropTypes.arrayOf(activityPropType);

export const childPropType = {
    links: React.PropTypes.shape({
        self: React.PropTypes.string.isRequired
    }).isRequired,
    name: React.PropTypes.string.isRequired,
    skills: React.PropTypes.arrayOf(
        React.PropTypes.arrayOf(
            React.PropTypes.number
        )).isRequired
};