import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {};

const reducer = handleActions({
        [Constants.USER_SIGNIN]: {
            next: (state, action) =>
                Object.assign(
                    {},
                    state,
                    {
                        idToken: action.payload.getAuthResponse().id_token,
                        name: action.payload.getBasicProfile().getName()
                    }),
            throw: state => state
        }
    },
    initialState);

export default reducer;