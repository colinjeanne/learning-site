import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {
    page: Constants.pages.ACTIVITIES
};

const reducer = handleActions({
        [Constants.SHOW_PAGE]: (state, action) =>
            Object.assign(
                {},
                state,
                { page: action.payload })
    },
    initialState);

export default reducer;