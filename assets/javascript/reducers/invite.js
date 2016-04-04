import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {
    invitations: [],
    invitedUserIds: []
};

const reducer = handleActions({
        [Constants.GET_MY_INVITATIONS]: {
            next: (state, action) => {
                if (!action.payload || action.error) {
                    return state;
                }
                
                return Object.assign(
                    {},
                    state,
                    {
                        invitations: action.payload
                    });
            },
            throw: state => state
        },
        
        [Constants.INVITE_FAMILY_MEMBER]: {
            next: (state, action) => {
                if (!action.payload || action.error) {
                    return state;
                }
                
                const invitedUserIds = [
                    action.payload,
                    ...state.invitedUserIds.filter(userId =>
                        userId !== action.payload)
                ];
                
                return Object.assign(
                    {},
                    state,
                    {
                        invitedUserIds
                    });
            },
            throw: state => state
        },
        
        [Constants.ACCEPT_INVITATION]: {
            next: (state, action) => {
                return Object.assign(
                    {},
                    state,
                    {
                        invitations: []
                    });
            },
            throw: state => state
        }
    },
    initialState);

export default reducer;