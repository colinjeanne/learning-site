import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {
    page: Constants.pages.UNAUTHORIZED
};

const reducer = handleActions({
        [Constants.SHOW_PAGE]: (state, action) =>
            Object.assign(
                {},
                state,
                { page: action.payload }),
        
        [Constants.USER_SIGNIN]: state =>
            Object.assign(
                {},
                state,
                { page: Constants.pages.ACTIVITIES })
    },
    initialState);

export default reducer;