import React from 'react';

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