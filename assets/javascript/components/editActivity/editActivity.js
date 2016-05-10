import ActivityLinksList from './activityLinksList';
import { activityPropType } from './../propTypes';
import AddActivityLink from './addActivityLink';
import React from 'react';

class EditActivity extends React.Component {
    constructor(props) {
        super(props);
        
        if (props.activity) {
            this.state = {
                name: props.activity.name,
                description: props.activity.description
            };
        } else {
            this.state = {
                name: '',
                description: ''
            };
        }
    }
    
    handleDescriptionChange(event) {
        this.setState({
            description: event.target.value
        });
    }
    
    handleNameChange(event) {
        this.setState({
            name: event.target.value
        });
    }
    
    handleAddLink(link) {
        const updatedActivityLinks = [
            ...this.props.activity.activityLinks,
            link
        ];
        
        const updatedActivity = Object.assign(
            {},
            this.props.activity,
            {
                activityLinks: updatedActivityLinks
            });
        this.props.onSave(updatedActivity);
    }
    
    handleDeleteLink(uri) {
        const updatedActivityLinks = this.props.activity.activityLinks.
            filter(activityLink => activityLink.uri !== uri);
        const updatedActivity = Object.assign(
            {},
            this.props.activity,
            {
                activityLinks: updatedActivityLinks
            });
        this.props.onSave(updatedActivity);
    }
    
    handleSave() {
        const updatedActivity = Object.assign(
            {},
            this.props.activity,
            {
                name: this.state.name.trim(),
                description: this.state.description
            });
        this.props.onSave(updatedActivity);
    }
    
    render() {
        const isCreate = !this.props.activity;
        const header = isCreate ? 'Add New Activity' : 'Edit Activity';
        
        const linksSection = !isCreate ? (
            <section>
                <h2>Links</h2>
                <ActivityLinksList
                    activityLinks={this.props.activity.activityLinks}
                    onDeleteLink={uri => this.handleDeleteLink(uri)} />
                <AddActivityLink
                    onAdd={link => this.handleAddLink(link)} />
            </section>
        ) : undefined;
        
        return (
            <section>
                <h1>
                    {header}
                </h1>
                <section className="activityEdit">
                    <section>
                        <label>
                            <h2>Name</h2>
                            <input
                                onChange={event =>
                                    this.handleNameChange(event)}
                                required="required"
                                size="60"
                                type="text"
                                value={this.state.name} />
                        </label>
                    </section>
                    <section>
                        <label>
                            <h2>Description</h2>
                            <textarea
                                cols="60"
                                onChange={event =>
                                    this.handleDescriptionChange(event)}
                                rows="10"
                                value={this.state.description}>
                            </textarea>
                        </label>
                    </section>
                    {linksSection}
                    <section
                        className="activityEditFooter">
                        <button
                            onClick={() => this.handleSave()}
                            type="button">
                            Save
                        </button>
                        <button
                            onClick={this.props.onCancel}
                            type="button">
                            Cancel
                        </button>
                    </section>
                </section>
            </section>
        );
    }
};

EditActivity.propTypes = {
    activity: activityPropType,
    onCancel: React.PropTypes.func.isRequired,
    onSave: React.PropTypes.func.isRequired
};

export default EditActivity;