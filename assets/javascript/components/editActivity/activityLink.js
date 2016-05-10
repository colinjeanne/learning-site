import { activityLinkPropType } from './../propTypes';
import React from 'react';

const activityLink = props => {
    return (
        <section className="activityLink">
            <span>{props.activityLink.title}</span>
            <span>
                (<a href={props.activityLink.uri}>{props.activityLink.uri}</a>)
            </span>
            <button
                onClick={() => props.onDelete(props.activityLink.uri)}>
                Delete
            </button>
        </section>
    );
};

activityLink.propTypes = {
    activityLink: activityLinkPropType.isRequired,
    onDelete: React.PropTypes.func.isRequired
};

export default activityLink;