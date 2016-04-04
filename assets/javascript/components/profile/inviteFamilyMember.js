import React from 'react';

class InviteFamilyMember extends React.Component {
    handleInvite() {
        const name = this.name.value.trim();
        if (name) {
            this.props.onInvite(name);
            this.name.value = '';
        }
    }
    
    render() {
        return (
            <section
                className="inviteFamilyMember">
                <label>
                    Invite family member:
                    <input
                        placeholder="User ID"
                        ref={elem => this.name = elem} />
                </label>
                <button
                    onClick={() => this.handleInvite()}
                    type="button">Invite</button>
            </section>
        );
    }
};

InviteFamilyMember.propTypes = {
    onInvite: React.PropTypes.func.isRequired
};

export default InviteFamilyMember;