import Activities from './activities';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import Constants from '../../constants/constants';
import {
    filterActivities,
    selectActivity,
    showPage } from '../../actions/navigation';
import { getActivity } from './../../actions/activity';

const mapStateToProps = state => {
    const activityFilter = state.navigation.activityFilter;
    const activities = state.activity.activities.filter(activity =>
        activityFilter ?
        activity.name.indexOf(activityFilter) !== -1 :
        true);
    return {
        activities
    };
};

const mapDispatchToProps = dispatch => {
    return bindActionCreators({
        onAddActivity: () => showPage(Constants.pages.ADD_ACTIVITY),
        onFilterActivities: filterActivities,
        onSelectActivity: activity => [
            getActivity(activity),
            selectActivity(activity.links.self)
        ]
    },
    dispatch);
};

export default connect(mapStateToProps, mapDispatchToProps)(Activities);