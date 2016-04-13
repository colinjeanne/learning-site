import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import Page from './page';
import { skillFiltered } from './../../actions/progress';
import { updateChild } from './../../actions/family';

const mapStateToProps = state => {
    return {
        children: state.user.children,
        skillFilter: state.progress.skillFilter
    };
};

const mapDispatchToProps = dispatch => {
    return bindActionCreators({
        onSkillChange: updateChild,
        onSkillFiltered: skillFiltered
    },
    dispatch);
};

export default connect(mapStateToProps, mapDispatchToProps)(Page);