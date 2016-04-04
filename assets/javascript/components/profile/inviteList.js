import React from 'react';

const inviteList = props => {
    const listItems = props.invitations.map(invite => (
        <li key={invite.id}>
            {'Invite from: ' + invite.createdBy}
            <button
                onClick={() => props.onAcceptInvitation(invite.id)}
                type="button">
                {'Accept'}
            </button>
        </li>
    ));
    
    const listNode = (
        <ul>
            {listItems}
        </ul>
    );
    
    const emptyMessageNode = (
        <div>{'You have no pending invitations.'}</div>
    );
    
    return listItems.length ? listNode : emptyMessageNode;
};

inviteList.propTypes = {
    invitations: React.PropTypes.arrayOf(
        React.PropTypes.shape({
            createdBy: React.PropTypes.string.isRequired,
            id: React.PropTypes.string.isRequired
        })).isRequired,
    onAcceptInvitation: React.PropTypes.func.isRequired
};

export default inviteList;