import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {
    me: {
        name: undefined
    },
    family: [],
    children: []
};

const reducer = handleActions({
        [Constants.USER_SIGNIN]: {
            next: (state, action) => {
                const updatedMe = Object.assign(
                    {},
                    state.me,
                    {
                        name: action.payload.getBasicProfile().getName()
                    });
                
                return Object.assign(
                    {},
                    state,
                    {
                        idToken: action.payload.getAuthResponse().id_token,
                        me: updatedMe
                    })
            },
            throw: state => state
        },
        
        [Constants.GET_ME]: {
            next: (state, action) => {
                if (!action.payload || action.error) {
                    return state;
                }
                
                const updatedMe = Object.assign(
                    {},
                    state.me,
                    {
                        id: action.payload.links.self
                    });
                
                return Object.assign(
                    {},
                    state,
                    {
                        me: updatedMe
                    });
            },
            throw: state => state
        },
        
        [Constants.GET_USER]: {
            next: (state, action) => state,
            throw: state => state
        },
        
        [Constants.UPDATE_ME]: {
            next: (state, action) => {
                if (!action.payload || action.error ||
                    (action.meta && action.meta.volatile)) {
                    return state;
                }
                
                const updatedMe = Object.assign(
                    {},
                    state.me,
                    {
                        name: action.payload.name,
                        id: action.payload.links.self
                    });
                
                return Object.assign(
                    {},
                    state,
                    {
                        me: updatedMe
                    });
            },
            throw: state => state
        },
        
        [Constants.GET_MY_FAMILY]: {
            next: (state, action) => {
                if (!action.payload || action.error) {
                    return state;
                }
                
                const family = action.payload.members.
                    filter(member => member.links.self !== state.me.id).
                    map(member => ({
                        name: member.name,
                        id: member.links.self
                    }));

                const children = action.payload.children;
                
                return Object.assign(
                    {},
                    state,
                    {
                        family,
                        children
                    });
            },
            throw: state => state
        },
        
        [Constants.ADD_CHILD]: {
            next: (state, action) => {
                if (!action.payload ||
                    (action.meta && action.meta.volatile) ||
                    action.error) {
                    return state;
                }
                
                const filteredChildren = state.children.filter(child =>
                    child.links.self !== action.payload.links.self);
                const children = [
                    ...filteredChildren,
                    action.payload
                ];
                
                return Object.assign(
                    {},
                    state,
                    {
                        children
                    });
            },
            throw: state => state
        },
        
        [Constants.UPDATE_CHILD]: {
            next: (state, action) => {
                if (!action.payload ||
                    (action.meta && action.meta.volatile) ||
                    action.error) {
                    return state;
                }
                
                const filteredChildren = state.children.filter(child =>
                    child.links.self !== action.payload.links.self);
                const children = [
                    ...filteredChildren,
                    action.payload
                ];
                
                return Object.assign(
                    {},
                    state,
                    {
                        children
                    });
            },
            throw: state => state
        }
    },
    initialState);

export default reducer;