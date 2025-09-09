import {Translator} from "../translations";


describe('Translations Service', () => {

    let $trans: Translator;

    beforeEach(() => {
        $trans = new Translator();
    });

    describe('choice()', () => {

        test('it pluralises as expected', () => {

            const cases = [
                {
                    translation: `cat`, count: 10000,
                    expected: `cat`,
                },
                {
                    translation: `cat|cats`, count: 1,
                    expected: `cat`,
                },
                {
                    translation: `cat|cats`, count: 0,
                    expected: `cats`,
                },
                {
                    translation: `cat|cats`, count: 2,
                    expected: `cats`,
                },
                {
                    translation: `{0} cat|[1,100] dog|[100,*] turtle`, count: 0,
                    expected: `cat`,
                },
                {
                    translation: `{0} cat|[1,100] dog|[100,*] turtle`, count: 40,
                    expected: `dog`,
                },
                {
                    translation: `{0} cat|[1,100] dog|[100,*] turtle`, count: 101,
                    expected: `turtle`,
                },
            ];

            for (const testCase of cases) {
                const output = $trans.choice(testCase.translation, testCase.count, {});
                expect(output).toEqual(testCase.expected);
            }
        });

        test('it replaces as expected', () => {
            const caseA = $trans.choice(`{0} cat|[1,100] :count dog|[100,*] turtle`, 4, {count: '5'});
            expect(caseA).toEqual('5 dog');

            const caseB = $trans.choice(`an :a :b :c dinosaur|many`, 1, {a: 'orange', b: 'angry', c: 'big'});
            expect(caseB).toEqual('an orange angry big dinosaur');
        });

        test('it provides count as a replacement by default', () => {
            const caseA = $trans.choice(`:count cats|:count dogs`, 4);
            expect(caseA).toEqual('4 dogs');
        });

        test('not provided replacements are left as-is', () => {
            const caseA = $trans.choice(`An :a dog`, 5, {});
            expect(caseA).toEqual('An :a dog');
        });

    });
});