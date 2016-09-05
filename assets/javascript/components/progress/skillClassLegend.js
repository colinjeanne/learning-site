import React from 'react';
import { ageClasses, ageClassNames } from './skillMethods';

const skillClassLegend = () => {
    const ageGroups = ageClasses.map(ageClass => (
        <li
            className={ageClass}
            key={ageClass}>
            <div>
                {ageClassNames[ageClass]}
            </div>
        </li>
    ));

    return (
        <section className="skillClassLegend">
            <h1>
                Age Group Legend
            </h1>
            <ol>
                {ageGroups}
            </ol>
        </section>
    );
};

export default skillClassLegend;