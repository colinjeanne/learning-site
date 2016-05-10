import { addActivity, updateActivity } from './../../actions/activity';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import Constants from './../../constants/constants';
import EditActivity from './editActivity';
import { showPage } from './../../actions/navigation';

const createOrUpdateActivity = activity =>
    (activity.links && activity.links.self) ?
    updateActivity(activity) :
    addActivity(activity.name, activity.description);

const mapStateToProps = state => {
    const selectedActivityId = state.navigation.selectedActivityId;
    const activity = selectedActivityId ?
        state.activity.activities.find(activity =>
            activity.links && activity.links.self === selectedActivityId) :
        undefined;
    
    return {
        activity
    };
};

const mapDispatchToProps = dispatch => {
    return bindActionCreators({
        onCancel: () => showPage(Constants.pages.ACTIVITIES),
        onSave: createOrUpdateActivity
    },
    dispatch);
};

export default connect(mapStateToProps, mapDispatchToProps)(EditActivity);