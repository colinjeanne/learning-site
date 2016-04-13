import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {
    skillFilter: 0
};

const reducer = handleActions({
        [Constants.SKILL_FILTERED]: (state, action) =>
            Object.assign(
                {},
                state,
                { skillFilter: action.payload })
    },
    initialState);

export default reducer;