import AddChild from './addChild';
import DataList from './dataList';
import InviteFamilyMember from './inviteFamilyMember';
import InviteList from './inviteList';
import React from 'react';

const familyMemberPropType = {
    id: React.PropTypes.string.isRequired,
    name: React.PropTypes.string.isRequired
};

const childPropType = {
    id: React.PropTypes.string.isRequired,
    name: React.PropTypes.string.isRequired,
    skills: React.PropTypes.arrayOf(
        React.PropTypes.arrayOf(
            React.PropTypes.number
        )).isRequired
};

const invitePropType = {
    createdBy: React.PropTypes.string.isRequired,
    id: React.PropTypes.string.isRequired
};

const profile = props => {
    const isInFamily = (props.family.length + props.children.length) > 0;
    
    const familyListData = props.family.map(member => ({
        text: member.name,
        key: member.id
    }));
    
    const childrenListData = props.children.map(child => ({
        text: child.name,
        key: child.id
    }));
    
    const invitationSection = (
        <section
            className="invitationSection">
            <h3>
                Invitations
            </h3>
            <InviteList
                invitations={props.invitations}
                onAcceptInvitation={props.onAcceptInvitation} />
        </section>
    );
    
    const noFamilyMembersMessage =
        'You have no family members currently, add some!';
    const noChildrenMessage =
        'You have no children currently, add some!';

    return (
        <section>
            <h1>
                {props.displayName}
            </h1>
            <section>
                <h2>
                    My Family
                </h2>
                <section
                    className="familyMemberSection">
                    <h3>
                        Members
                    </h3>
                    <DataList
                        emptyMessage={noFamilyMembersMessage}
                        items={familyListData} />
                    <InviteFamilyMember
                        onInvite={props.onInviteFamilyMember} />
                </section>
                <section
                    className="childrenSection">
                    <h3>
                        Children
                    </h3>
                    <DataList
                        emptyMessage={noChildrenMessage}
                        items={childrenListData} />
                    <AddChild
                        onAdd={props.onAddChild} />
                </section>
                {!isInFamily ? invitationSection : undefined}
            </section>
        </section>
    )};

profile.propTypes = {
    children: React.PropTypes.arrayOf(
        React.PropTypes.shape(childPropType)),
    displayName: React.PropTypes.string,
    family: React.PropTypes.arrayOf(
        React.PropTypes.shape(familyMemberPropType)),
    invitations: React.PropTypes.arrayOf(
        React.PropTypes.shape(invitePropType)),
    onAcceptInvitation: React.PropTypes.func.isRequired,
    onAddChild: React.PropTypes.func.isRequired,
    onInviteFamilyMember: React.PropTypes.func.isRequired
};

export default profile;