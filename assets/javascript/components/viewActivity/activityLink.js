import { activityLinkPropType } from './../propTypes';
import React from 'react';

const activityLink = props => {
    const isImage = /\.(bmp|gif|jpeg|jpg|png|svg)$/.
        test(props.activityLink.uri);
    const dataElement = isImage ?
        (
            <img src={props.activityLink.uri} />
        ) :
        (
            <a href={props.activityLink.uri}>{props.activityLink.title}</a>
        );

    return (
        <section className="activityLink">
            {dataElement}
        </section>
    );
};

activityLink.propTypes = {
    activityLink: activityLinkPropType.isRequired
};

export default activityLink;