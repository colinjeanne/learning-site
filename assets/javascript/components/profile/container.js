import {
    acceptInvitation,
    inviteFamilyMember } from '../../actions/invite';
import { addChild } from '../../actions/family';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import Profile from './profile';

const mapStateToProps = state => {
    return {
        children: state.user.children,
        displayName: state.user.name,
        family: state.user.family,
        invitations: state.invite.invitations
    };
};

const mapDispatchToProps = dispatch => {
    return bindActionCreators({
        onAcceptInvitation: acceptInvitation,
        onAddChild: addChild,
        onInviteFamilyMember: inviteFamilyMember
    },
    dispatch);
};

export default connect(mapStateToProps, mapDispatchToProps)(Profile);