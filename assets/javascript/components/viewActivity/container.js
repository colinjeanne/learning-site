import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import Constants from './../../constants/constants';
import ViewActivity from './viewActivity';
import { showPage } from './../../actions/navigation';

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
        onBack: () => showPage(Constants.pages.ACTIVITIES),
        onEdit: () => showPage(Constants.pages.EDIT_ACTIVITY)
    },
    dispatch);
};

export default connect(mapStateToProps, mapDispatchToProps)(ViewActivity);