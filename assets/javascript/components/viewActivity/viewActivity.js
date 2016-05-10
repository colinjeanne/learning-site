import ActivityLinksList from './activityLinksList';
import { activityPropType } from './../propTypes';
import React from 'react';

const viewActivity = props => {
    const linksSection = props.activity.activityLinks ?
        (
            <section>
                <h2>Links</h2>
                <ActivityLinksList
                    activityLinks={props.activity.activityLinks} />
            </section>
        ) :
        undefined;
    
    return (
        <section>
            <h1>
                {props.activity.name}
            </h1>
            <section className="activityView">
                <section>
                    {props.activity.description}
                </section>
                {linksSection}
                <section
                    className="activityViewFooter">
                    <button
                        onClick={props.onBack}
                        type="button">
                        Back
                    </button>
                    <button
                        onClick={props.onEdit}
                        type="button">
                        Edit
                    </button>
                </section>
            </section>
        </section>
    );
};

viewActivity.propTypes = {
    activity: activityPropType.isRequired,
    onBack: React.PropTypes.func.isRequired,
    onEdit: React.PropTypes.func.isRequired
};

export default viewActivity;